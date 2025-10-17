<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * GET /api/available-slots
     * Retorna os slots disponíveis para o calendário.
     */
    public function getAvailableSlots(Request $request)
    {
        // ** Implementação básica: Exemplo de slots de 30 minutos. **
        $date = $request->input('date', Carbon::today()->toDateString());
        $startOfDay = Carbon::parse($date)->hour(9)->minute(0)->second(0); // 09:00
        $endOfDay = Carbon::parse($date)->hour(17)->minute(0)->second(0); // 17:00
        $slotDuration = 30; // minutos

        $slots = [];
        $current = $startOfDay->copy();

        while ($current->lessThan($endOfDay)) {
            $slots[] = $current->toDateTimeString();
            $current->addMinutes($slotDuration);
        }

        // Busca agendamentos PENDENTES ou CONFIRMADOS no banco para o dia
        $bookedSlots = Appointment::whereDate('scheduled_at', $date)
                                 ->whereIn('status', ['pending', 'confirmed'])
                                 ->pluck('scheduled_at')
                                 ->map(fn($ts) => Carbon::parse($ts)->toDateTimeString())
                                 ->toArray();

        $availableSlots = array_values(array_diff($slots, $bookedSlots));

        return response()->json(['available_slots' => $availableSlots]);
    }

    /**
     * POST /api/appointments/initiate
     * 1. Registra/Busca Paciente (FindOrCreate). 2. Tenta reservar. 3. Gera Registro de Pagamento.
     */
    public function initiateAppointment(Request $request)
    {
        // 1. Validação dos Dados Mínimos
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'cpf' => 'required|string|size:11', 
            'scheduled_at' => 'required|date_format:Y-m-d H:i:s'
        ]);

        $scheduledAt = Carbon::parse($request->scheduled_at);

        // --- MELHORIA: FIND OR CREATE PATIENT ---
        // Se o paciente existir, ele será usado. Se não, será criado.
        $patient = Patient::firstOrCreate(
            ['cpf' => $request->cpf],
            [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]
        );
        // ----------------------------------------

        // Inicia Transação para garantir atomicidade
        return DB::transaction(function () use ($request, $scheduledAt, $patient) {
            
            // 2. Verificar Disponibilidade (re-checar por segurança)
            $existingAppointment = Appointment::where('scheduled_at', $scheduledAt)
                                             ->whereIn('status', ['pending', 'confirmed'])
                                             ->lockForUpdate()
                                             ->first();
            
            if ($existingAppointment) {
                return response()->json(['message' => 'O horário não está mais disponível.'], 409);
            }
            
            // 3. Criação do Agendamento (Status PENDING) - SEM payment_id AINDA
            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'scheduled_at' => $scheduledAt,
                'status' => 'pending',
                'payment_id' => null, // Começa como nulo
            ]);

            // 4. Criação do Registro de Pagamento INICIAL
            $amount = 150.00; 
            
            // Cria o pagamento, vinculando-o IMEDIATAMENTE ao agendamento
            // Usa o relacionamento 'payment()' no modelo Appointment.
            $payment = $appointment->payment()->create([ 
                'amount' => $amount,
                'status' => 'pending',
                'method' => 'pix' // Método padrão para seguir para a tela de pagamento
            ]);
            
            // 5. ATUALIZA o agendamento com o ID do pagamento
            $appointment->payment_id = $payment->id;
            $appointment->save();

            // 6. Resposta: Retorna dados para ir para a tela de pagamento
            return response()->json([
                'message' => 'Agendamento iniciado. Prossiga para o pagamento.',
                'appointment_id' => $appointment->id,
                'payment_id' => $payment->id,
                'amount' => number_format($amount, 2, ',', '.')
            ], 201);
        });
    }

    /**
     * GET /api/appointments
     * Lista todos os agendamentos, com filtros opcionais por data e status.
     * Útil para uma área administrativa.
     */
    public function listAppointments(Request $request)
    {
        // Inicia a query com os relacionamentos necessários (Patient e Payment)
        $query = Appointment::with(['patient', 'payment']);

        // Filtro por Data (ex: /api/appointments?date=2025-10-15)
        if ($request->filled('date')) {
            $query->whereDate('scheduled_at', $request->date);
        }

        // Filtro por Status (ex: /api/appointments?status=confirmed)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Ordena por data e hora do agendamento (mais recentes primeiro)
        $appointments = $query->orderBy('scheduled_at', 'desc')->get();

        // Mapeia os resultados para uma estrutura de resposta limpa
        $formattedAppointments = $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'scheduled_at' => $appointment->scheduled_at,
                'status' => $appointment->status,
                'patient' => [
                    'id' => $appointment->patient->id,
                    'name' => $appointment->patient->name,
                    'cpf' => $appointment->patient->cpf,
                    'email' => $appointment->patient->email,
                ],
                'payment' => $appointment->payment ? [
                    'id' => $appointment->payment->id,
                    'amount' => number_format($appointment->payment->amount, 2, '.', ''),
                    'status' => $appointment->payment->status,
                ] : null,
            ];
        });

        return response()->json(['appointments' => $formattedAppointments]);
    }

    /**
     * POST /api/appointments/{id}/cancel
     * Cancela um agendamento confirmado a pedido (simulando estorno do pagamento).
     */
    public function cancelAppointment($id)
    {
        $appointment = Appointment::with('payment')
                                 ->findOrFail($id);

        if ($appointment->status === 'canceled') {
            return response()->json(['message' => 'O agendamento já está cancelado.'], 200);
        }

        // Somente agendamentos confirmados podem ser cancelados e potencialmente estornados
        if ($appointment->status !== 'confirmed') {
            return response()->json([
                'message' => 'Apenas agendamentos confirmados podem ser cancelados via este endpoint (Status atual: ' . $appointment->status . ').'
            ], 400);
        }

        // Inicia transação para garantir que o agendamento e o pagamento sejam atualizados
        return DB::transaction(function () use ($appointment) {
            
            // 1. Atualiza o status do agendamento
            $appointment->status = 'canceled';
            $appointment->save();

            // 2. Simula o Estorno do Pagamento
            if ($appointment->payment && $appointment->payment->status === 'approved') {
                $payment = $appointment->payment;
                $payment->status = 'refunded'; // Marca o pagamento como estornado
                $payment->save();
                
                $message = 'Agendamento cancelado com sucesso. O pagamento associado foi marcado como "estornado".';
            } else {
                $message = 'Agendamento cancelado com sucesso.';
            }

            return response()->json([
                'message' => $message,
                'appointment_id' => $appointment->id,
                'new_status' => 'canceled'
            ], 200);
        });
    }

    /**
     * GET /api/appointments/{id}/confirmation
     * Retorna o resumo (só se o pagamento estiver CONFIRMADO).
     */
    public function getConfirmationDetails($id)
    {
        $appointment = Appointment::with(['patient', 'payment'])
                                 ->findOrFail($id);

        if ($appointment->status !== 'confirmed') {
            
            // Se o agendamento foi cancelado, sugere reprocessar o pagamento.
            if ($appointment->status === 'canceled' && $appointment->payment_id) {
                return response()->json([
                    'message' => 'O agendamento foi cancelado (pagamento falhou ou foi estornado). Por favor, inicie um novo ciclo de pagamento.',
                    'status' => 'canceled',
                    'can_reprocess' => true,
                    // Retorna o último ID de pagamento associado, que pode ser o falho ou o estornado.
                    'last_payment_id' => $appointment->payment_id 
                ], 403);
            }

            return response()->json([
                'message' => 'Pagamento pendente ou agendamento não confirmado.',
                'status' => $appointment->status,
                'can_reprocess' => false
            ], 403);
        }

        // Resumo
        return response()->json([
            'message' => 'Agendamento e Pagamento Confirmados!',
            'appointment' => [
                'id' => $appointment->id,
                'patient' => $appointment->patient->name,
                'scheduled_at' => $appointment->scheduled_at,
                'status' => $appointment->status,
            ],
            'payment' => [
                'amount' => number_format($appointment->payment->amount, 2, '.', ''), // Mudança para retornar ponto (.) como separador decimal para JSON
                'method' => $appointment->payment->method,
                'status' => $appointment->payment->status,
                'transaction_id' => $appointment->payment->transaction_id,
            ]
        ]);
    }
}
