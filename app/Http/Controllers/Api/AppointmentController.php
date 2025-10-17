<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AppointmentController extends Controller
{
    /**
     * Gera uma lista de horários disponíveis para uma data específica.
     * Agora, exclui slots que estão 'confirmed' ou 'pending' DENTRO do prazo de 5 minutos.
     */
    public function getAvailableSlots(Request $request)
    {
        // Validação da data
        $request->validate(['date' => 'required|date_format:Y-m-d']);
        $date = $request->date;

        // 1. Definição das regras de negócio (ex: clínica aberta das 9:00 às 17:00, slots de 30 min)
        $startHour = Carbon::parse("$date 09:00:00");
        $endHour = Carbon::parse("$date 17:00:00");
        $interval = 30; // minutos
        $reservationTimeoutMinutes = 5; // Tempo de reserva do slot para pagamento

        // 2. Geração de todos os slots potenciais
        $allSlots = [];
        $period = CarbonPeriod::since($startHour)->minutes($interval)->until($endHour);

        foreach ($period as $time) {
            // Exclui o horário de fechamento se não for um slot de início
            if ($time->equalTo($endHour)) continue;
            $allSlots[] = $time->toDateTimeString();
        }

        // 3. Busca de agendamentos ocupados na data
        $bookedAppointments = Appointment::whereDate('scheduled_at', $date)
            ->where(function ($query) use ($reservationTimeoutMinutes) {
                // Condição 1: Status confirmado (sempre ocupado)
                $query->where('status', 'confirmed')
                      
                // Condição 2: Status pendente E o agendamento foi criado há MENOS de X minutos
                      ->orWhere(function ($query) use ($reservationTimeoutMinutes) {
                          $query->where('status', 'pending')
                                // created_at deve ser maior que (agora - 5 minutos)
                                ->where('created_at', '>', Carbon::now()->subMinutes($reservationTimeoutMinutes));
                      });
            })
            ->get();

        // 4. Extração dos horários ocupados
        $bookedSlots = $bookedAppointments->map(function ($appointment) {
            // Garante o formato compatível com os slots gerados
            return Carbon::parse($appointment->scheduled_at)->toDateTimeString();
        })->toArray();

        // 5. Filtragem: Remove slots ocupados da lista total
        $availableSlots = array_diff($allSlots, $bookedSlots);

        return response()->json([
            'available_slots' => array_values($availableSlots),
        ]);
    }

    /**
     * Inicia o agendamento (reserva o slot e gera o pagamento).
     */
    public function initiateAppointment(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'cpf' => 'required|string|size:11',
            'scheduled_at' => 'required|date_format:Y-m-d H:i:s',
        ]);

        $scheduledAt = $validated['scheduled_at'];
        $reservationTimeoutMinutes = 5;

        // VERIFICAÇÃO DE DUPLA RESERVA (ANTES DE INICIAR)
        $existingAppointment = Appointment::where('scheduled_at', $scheduledAt)
            ->where(function ($query) use ($reservationTimeoutMinutes) {
                // Checa por agendamentos CONFIRMED
                $query->where('status', 'confirmed')
                    // Checa por agendamentos PENDING que AINDA não expiraram (criados há < 5 min)
                    ->orWhere(function ($query) use ($reservationTimeoutMinutes) {
                        $query->where('status', 'pending')
                            ->where('created_at', '>', Carbon::now()->subMinutes($reservationTimeoutMinutes));
                    });
            })
            ->first();

        if ($existingAppointment) {
            return response()->json(['message' => 'Este horário não está mais disponível. Por favor, selecione outro.'], 409);
        }

        // 1. Lógica de Busca/Criação de Paciente
        $patient = Patient::firstOrCreate(
            ['cpf' => $validated['cpf']],
            ['name' => $validated['name'], 'email' => $validated['email'], 'phone' => $validated['phone']]
        );

        // 2. Lógica de Criação do Pagamento (Simulação)
        $paymentAmount = 150.00; // Valor fixo para consulta
        $payment = Payment::create([
            'patient_id' => $patient->id,
            'amount' => $paymentAmount,
            'status' => 'pending',
            'transaction_id' => null,
            'metadata' => ['service' => 'Consulta Médica Inicial'],
        ]);

        // 3. Lógica de Criação do Agendamento
        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'payment_id' => $payment->id, // Vincula o pagamento ao agendamento
            'scheduled_at' => $scheduledAt,
            'status' => 'pending', // Reservado, mas aguardando pagamento
            'metadata' => [],
        ]);

        return response()->json([
            'message' => 'Agendamento iniciado com sucesso. Aguardando pagamento.',
            'appointment_id' => $appointment->id,
            'payment_id' => $payment->id, // Retorna o ID do pagamento para o frontend
            'amount' => $paymentAmount,
        ], 200);
    }

    // --- Outros Métodos (simulados) ---

    public function getConfirmationDetails($id)
    {
        $appointment = Appointment::with('payment')->find($id);

        if (!$appointment) {
            return response()->json(['message' => 'Agendamento não encontrado.'], 404);
        }

        // Dados do paciente
        $patient = Patient::find($appointment->patient_id);
        
        // Simulação de reprocessamento (se cancelado e houver pagamento anterior)
        $canReprocess = $appointment->status === 'canceled' && $appointment->payment_id !== null;
        $lastPaymentId = $appointment->payment_id;

        return response()->json([
            'status' => $appointment->status,
            'message' => 'Detalhes da confirmação.',
            'can_reprocess' => $canReprocess,
            'last_payment_id' => $lastPaymentId,
            'appointment' => [
                'id' => $appointment->id,
                'patient' => $patient->name,
                'scheduled_at' => $appointment->scheduled_at,
            ],
            'payment' => [
                'amount' => $appointment->payment->amount,
                'transaction_id' => $appointment->payment->transaction_id ?? 'N/A',
                'status' => $appointment->payment->status,
            ]
        ]);
    }

    public function cancelAppointment(Request $request, $id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json(['message' => 'Agendamento não encontrado.'], 404);
        }

        // Só cancela se for pending ou confirmed
        if ($appointment->status === 'canceled') {
            return response()->json(['message' => 'Agendamento já está cancelado.'], 400);
        }

        $appointment->status = 'canceled';
        $appointment->save();

        // Lógica para estornar pagamento, se confirmed
        if ($appointment->payment_id) {
            $payment = Payment::find($appointment->payment_id);
            if ($payment && $payment->status === 'approved') {
                // Lógica de estorno (simulada)
                $payment->status = 'refunded';
                $payment->save();
            }
        }

        return response()->json(['message' => 'Agendamento cancelado com sucesso.'], 200);
    }

    // Método dummy para listagem admin (assumindo a rota foi adicionada)
    public function listAppointments(Request $request)
    {
        $appointments = Appointment::with(['patient', 'payment'])
            ->orderBy('scheduled_at', 'desc')
            ->limit(100) // Limita a 100 para simulação
            ->get();

        return response()->json($appointments);
    }
}
