<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Payment;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StripeController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Cria um Payment Intent no Stripe
     */
    public function createPaymentIntent(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:1',
                'currency' => 'required|string|max:3',
                'appointment_data' => 'required|array',
                'appointment_data.patient_name' => 'required|string|max:255',
                'appointment_data.patient_email' => 'required|email',
                'appointment_data.scheduled_at' => 'required|date',
                'appointment_data.doctor_id' => 'required|integer|exists:users,id',
                'appointment_data.patient_id' => 'required|integer|exists:patients,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Dados inválidos',
                    'details' => $validator->errors()
                ], 422);
            }

            $appointmentData = $request->input('appointment_data');
            $amount = $request->input('amount');
            $currency = $request->input('currency', 'brl');

            // Converter para centavos se for BRL
            if ($currency === 'brl') {
                $amount = (int) ($amount * 100);
            }

            $paymentIntent = $this->stripeService->createPaymentIntent([
                'amount' => $amount,
                'currency' => $currency,
                'metadata' => [
                    'patient_name' => $appointmentData['patient_name'],
                    'patient_email' => $appointmentData['patient_email'],
                    'scheduled_at' => $appointmentData['scheduled_at'],
                    'doctor_id' => $appointmentData['doctor_id'],
                    'patient_id' => $appointmentData['patient_id'],
                    'app_name' => config('app.name'),
                ]
            ]);

            return response()->json([
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao criar Payment Intent: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Erro interno do servidor',
                'message' => 'Não foi possível processar o pagamento. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Confirma o pagamento e cria o agendamento
     */
    public function confirmPayment(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'payment_intent_id' => 'required|string',
                'appointment_data' => 'required|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Dados inválidos',
                    'details' => $validator->errors()
                ], 422);
            }

            $paymentIntentId = $request->input('payment_intent_id');
            $appointmentData = $request->input('appointment_data');

            // Verificar status do pagamento no Stripe
            $paymentIntent = $this->stripeService->retrievePaymentIntent($paymentIntentId);

            if ($paymentIntent->status !== 'succeeded') {
                return response()->json([
                    'error' => 'Pagamento não confirmado',
                    'status' => $paymentIntent->status
                ], 400);
            }

            // Processar transação no banco de dados
            DB::beginTransaction();

            try {
                // Criar agendamento
                $appointment = Appointment::create([
                    'patient_id' => $appointmentData['patient_id'],
                    'doctor_id' => $appointmentData['doctor_id'],
                    'scheduled_at' => $appointmentData['scheduled_at'],
                    'status' => 'confirmed',
                    'notes' => $appointmentData['notes'] ?? null,
                ]);

                // Registrar pagamento
                $payment = Payment::create([
                    'appointment_id' => $appointment->id,
                    'amount' => $paymentIntent->amount / 100, // Converter de centavos
                    'currency' => $paymentIntent->currency,
                    'method' => 'stripe',
                    'status' => 'completed',
                    'stripe_payment_intent_id' => $paymentIntent->id,
                    'stripe_payment_method_id' => $paymentIntent->payment_method,
                    'transaction_date' => now(),
                    'metadata' => json_encode([
                        'stripe_charges' => $paymentIntent->charges->data ?? [],
                        'receipt_url' => $paymentIntent->charges->data[0]->receipt_url ?? null,
                    ]),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'appointment' => $appointment->load(['patient', 'doctor']),
                    'payment' => $payment,
                    'receipt_url' => $paymentIntent->charges->data[0]->receipt_url ?? null,
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Erro ao confirmar pagamento: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Erro interno do servidor',
                'message' => 'Não foi possível confirmar o pagamento. Entre em contato conosco.'
            ], 500);
        }
    }

    /**
     * Webhook do Stripe para processar eventos
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            $payload = $request->getContent();
            $sigHeader = $request->header('Stripe-Signature');
            $webhookSecret = config('services.stripe.webhook_secret');

            $event = $this->stripeService->verifyWebhookSignature($payload, $sigHeader, $webhookSecret);

            // Log do evento para debugging
            Log::info('Stripe Webhook recebido', [
                'type' => $event->type,
                'id' => $event->id
            ]);

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentSucceeded($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailed($event->data->object);
                    break;

                case 'payment_intent.canceled':
                    $this->handlePaymentCanceled($event->data->object);
                    break;

                default:
                    Log::info('Evento Stripe não tratado: ' . $event->type);
                    break;
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Erro no webhook Stripe: ' . $e->getMessage(), [
                'payload' => $request->getContent(),
                'headers' => $request->headers->all()
            ]);

            return response()->json(['error' => 'Webhook error'], 400);
        }
    }

    /**
     * Processa pagamento bem-sucedido
     */
    private function handlePaymentSucceeded($paymentIntent): void
    {
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($payment) {
            $payment->update([
                'status' => 'completed',
                'transaction_date' => now(),
            ]);

            // Atualizar status do agendamento
            $payment->appointment->update([
                'status' => 'confirmed'
            ]);

            Log::info('Pagamento confirmado via webhook', [
                'payment_id' => $payment->id,
                'appointment_id' => $payment->appointment_id
            ]);
        }
    }

    /**
     * Processa falha no pagamento
     */
    private function handlePaymentFailed($paymentIntent): void
    {
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($payment) {
            $payment->update([
                'status' => 'failed',
                'transaction_date' => now(),
            ]);

            // Cancelar agendamento
            $payment->appointment->update([
                'status' => 'canceled'
            ]);

            Log::warning('Pagamento falhou via webhook', [
                'payment_id' => $payment->id,
                'appointment_id' => $payment->appointment_id
            ]);
        }
    }

    /**
     * Processa cancelamento do pagamento
     */
    private function handlePaymentCanceled($paymentIntent): void
    {
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($payment) {
            $payment->update([
                'status' => 'canceled',
                'transaction_date' => now(),
            ]);

            // Cancelar agendamento
            $payment->appointment->update([
                'status' => 'canceled'
            ]);

            Log::info('Pagamento cancelado via webhook', [
                'payment_id' => $payment->id,
                'appointment_id' => $payment->appointment_id
            ]);
        }
    }
}