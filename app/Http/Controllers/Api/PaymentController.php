<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Appointment; // Incluir para poder mexer no Appointment
use Illuminate\Support\Facades\DB; // Incluir para usar transações
use Carbon\Carbon;

class PaymentController extends Controller
{
    // --- ROTAS DE LEITURA ---

    /**
     * GET /api/payments/{id}
     * Retorna os detalhes de um registro de pagamento específico.
     * Adicionamos tratamento de erro para status "failed" e "refunded".
     */
    public function getPaymentDetails($id)
    {
        // Busca o pagamento e falha se não encontrar (retorna 404)
        $payment = Payment::findOrFail($id);
        
        // Novo tratamento de status para informar o cliente sobre transações falhas
        if ($payment->status === 'failed') {
            return response()->json([
                'message' => 'O pagamento falhou. Por favor, tente novamente ou escolha outro método.',
                'status' => 'failed',
                'amount' => number_format($payment->amount, 2, ',', '.')
            ], 403);
        }

        if ($payment->status === 'refunded') {
            return response()->json([
                'message' => 'O pagamento foi estornado. O agendamento foi cancelado.',
                'status' => 'refunded',
                'amount' => number_format($payment->amount, 2, ',', '.')
            ], 403);
        }


        // Se o status for 'pending' ou 'approved', retorna os detalhes completos
        return response()->json([
            'id' => $payment->id,
            'appointment_id' => $payment->appointment_id,
            'amount' => number_format($payment->amount, 2, ',', '.'),
            'status' => $payment->status,
            'method' => $payment->method,
            'transaction_id' => $payment->transaction_id,
        ]);
    }

    // --- ROTAS DE AÇÃO ---

    /**
     * POST /api/payments/{id}/process
     * Simula o envio da requisição para um gateway de pagamento (Pix, Cartão, etc.).
     * Retorna os dados necessários para o cliente concluir a transação (QR Code, Link).
     */
    public function processPayment(Request $request, $id)
    {
        $request->validate([
            'method' => 'required|in:pix,credit_card'
        ]);

        // Procura apenas pagamentos PENDENTES
        $payment = Payment::where('id', $id)
                          ->where('status', 'pending')
                          ->firstOrFail();

        // 1. Simulação: Gerar um ID de Transação Real
        // Em um sistema real, aqui você faria a chamada à API do PSP (Provedor de Serviço de Pagamento)
        $transactionId = $this->generateSimulatedTransactionId($payment->id);
        
        // 2. Atualiza o registro de pagamento no banco (com o ID da transação)
        $payment->update([
            'method' => $request->method,
            'transaction_id' => $transactionId,
            // O status continua 'pending' até o Webhook notificar.
        ]);

        // 3. Monta a Resposta (Baseada na sua sugestão JSON)
        $responseDetails = $this->getSimulatedTransactionDetails($transactionId, $request->method);

        return response()->json([
            'message' => 'Transação iniciada. Aguardando confirmação.',
            'transaction_details' => $responseDetails
        ]);
    }
    
    /**
     * POST /api/payments/{id}/reprocess
     * Cria um novo registro de pagamento para um agendamento existente que foi cancelado.
     * O $id refere-se ao ID do PAGAMENTO original.
     */
    public function reprocessPayment(Request $request, $id)
    {
        // 1. Busca o pagamento original (que deve estar failed, refunded, ou já approved)
        $originalPayment = Payment::findOrFail($id);
        
        $appointment = $originalPayment->appointment;

        // Verifica se o agendamento permite reprocessamento (se está cancelado)
        if ($appointment->status !== 'canceled') {
            return response()->json([
                'message' => 'O agendamento não está em um estado que permita um novo pagamento (status: ' . $appointment->status . ').'
            ], 400);
        }
        
        // Inicia a transação para garantir que o Appointment e o novo Payment sejam atualizados juntos
        return DB::transaction(function () use ($appointment) {
            
            // 2. Cria um NOVO registro de pagamento (com status 'pending')
            $newPayment = Payment::create([
                'appointment_id' => $appointment->id,
                'amount' => $originalPayment->amount, // Mantém o mesmo valor
                'status' => 'pending',
                'method' => 'pix', // Valor inicial
                'transaction_id' => null,
            ]);

            // 3. Atualiza o agendamento para apontar para o NOVO ID de pagamento
            $appointment->payment_id = $newPayment->id;
            $appointment->status = 'pending'; // Volta o agendamento para pending
            $appointment->save();

            // 4. Retorna os detalhes do NOVO pagamento (para ir para a tela de processamento)
            return response()->json([
                'message' => 'Novo ciclo de pagamento iniciado. Prossiga para o processamento.',
                'appointment_id' => $appointment->id,
                'payment_id' => $newPayment->id,
                'amount' => number_format($newPayment->amount, 2, ',', '.')
            ], 201);
        });
    }

    /**
     * POST /api/payments/webhook
     * Recebe notificações do gateway de pagamento (PSP) sobre o status da transação.
     */
    public function handleWebhook(Request $request)
    {
        // 1. Simulação: Validação de Segurança (Chave Secreta/Assinatura)
        // Em um sistema real, esta é a parte mais crítica.

        $request->validate([
            'transaction_id' => 'required|string',
            'status' => 'required|in:approved,failed,refunded'
        ]);

        // Busca o pagamento pelo transaction_id (que foi gerado no passo anterior)
        $payment = Payment::where('transaction_id', $request->transaction_id)->first();

        if (!$payment) {
            // Retorna 200 para não reativar o webhook, mas loga o erro
            return response()->json(['message' => 'Transaction ID not found (logged)'], 200);
        }

        // 2. Atualiza o status do pagamento
        $payment->status = $request->status;
        $payment->save();

        // 3. Atualiza o status do agendamento APENAS se o pagamento foi aprovado
        // E cancela o agendamento se for reembolsado/falhou
        $appointment = $payment->appointment; // Acessa o relacionamento belongsTo
        if ($appointment) {
            if ($payment->status === 'approved') {
                $appointment->status = 'confirmed';
            } elseif ($payment->status === 'refunded' || $payment->status === 'failed') {
                // Se falhou/estornou, cancelamos para liberar o slot e permitir reprocessamento.
                $appointment->status = 'canceled'; 
            }
            $appointment->save();
        }


        // Retorno 200 OK é o padrão para Webhooks
        return response(null, 200);
    }


    // --- MÉTODOS PRIVADOS DE SIMULAÇÃO ---

    /**
     * Simula a geração de um ID de transação.
     */
    private function generateSimulatedTransactionId($paymentId)
    {
        return 'TRX_' . time() . '_' . $paymentId;
    }

    /**
     * Simula os detalhes que o gateway retornaria (QR Code, Link).
     */
    private function getSimulatedTransactionDetails($transactionId, $method)
    {
        if ($method === 'pix') {
            return [
                "transaction_id" => $transactionId,
                "qr_code_base64" => "base64_do_qr_code_simulado_" . $transactionId,
                "copy_paste_code" => "00020101021226580014br.gov.bcb.pix..." . substr(md5($transactionId), 0, 10),
            ];
        }

        if ($method === 'credit_card') {
            return [
                "transaction_id" => $transactionId,
                "payment_link" => "https://gateway.com/pay/link/" . $transactionId,
                "token_id" => "card_token_" . time()
            ];
        }

        return ['transaction_id' => $transactionId];
    }
}
