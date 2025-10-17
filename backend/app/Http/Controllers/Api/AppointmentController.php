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
     * Considera agendamentos ocupados, bloqueios de data e configurações da clínica.
     */
    public function getAvailableSlots(Request $request)
    {
        // Validação da data
        $request->validate(['date' => 'required|date_format:Y-m-d']);
        $date = $request->date;

        // Verificar se a data inteira está bloqueada
        $isDateFullyBlocked = \App\Models\ScheduleBlock::where('is_active', true)
            ->where('date', $date)
            ->where('type', 'full_day')
            ->exists();

        if ($isDateFullyBlocked) {
            return response()->json([
                'available_slots' => [],
                'blocked_reason' => 'Data totalmente bloqueada'
            ]);
        }

        // 1. Obter configurações da clínica
        $workStartTime = \App\Models\Setting::get('work_start_time', '09:00');
        $workEndTime = \App\Models\Setting::get('work_end_time', '17:00');
        $appointmentDuration = \App\Models\Setting::get('appointment_duration', 30);
        $workDays = \App\Models\Setting::get('work_days', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);

        // Verificar se o dia da semana está configurado para trabalho
        $dayOfWeek = Carbon::parse($date)->format('l'); // Monday, Tuesday, etc.
        $dayOfWeekLower = strtolower($dayOfWeek);
        
        if (!in_array($dayOfWeekLower, $workDays)) {
            return response()->json([
                'available_slots' => [],
                'blocked_reason' => 'Clínica fechada neste dia da semana'
            ]);
        }

        // 2. Definição das regras de negócio
        $startHour = Carbon::parse("$date $workStartTime");
        $endHour = Carbon::parse("$date $workEndTime");
        $interval = (int) $appointmentDuration; // minutos
        $reservationTimeoutMinutes = 5; // Tempo de reserva do slot para pagamento

        // 3. Geração de todos os slots potenciais
        $allSlots = [];
        $period = CarbonPeriod::since($startHour)->minutes($interval)->until($endHour);

        foreach ($period as $time) {
            // Exclui o horário de fechamento se não for um slot de início
            if ($time->equalTo($endHour)) continue;
            $allSlots[] = $time->toDateTimeString();
        }

        // 4. Busca de agendamentos ocupados na data
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

        // 5. Extração dos horários ocupados
        $bookedSlots = $bookedAppointments->map(function ($appointment) {
            // Garante o formato compatível com os slots gerados
            return Carbon::parse($appointment->scheduled_at)->toDateTimeString();
        })->toArray();

        // 6. Buscar bloqueios parciais de horário para esta data
        $timeRangeBlocks = \App\Models\ScheduleBlock::where('is_active', true)
            ->where('date', $date)
            ->where('type', 'time_range')
            ->get();

        // 7. Filtrar slots que estão dentro de bloqueios de horário
        $blockedSlots = [];
        foreach ($allSlots as $slot) {
            $slotTime = Carbon::parse($slot);
            
            foreach ($timeRangeBlocks as $block) {
                $blockStart = Carbon::parse($date . ' ' . $block->start_time);
                $blockEnd = Carbon::parse($date . ' ' . $block->end_time);
                
                // Verificar se o slot está dentro do período bloqueado
                if ($slotTime->between($blockStart, $blockEnd->subMinute())) {
                    $blockedSlots[] = $slot;
                    break;
                }
            }
        }

        // 8. Filtragem: Remove slots ocupados e bloqueados da lista total
        $unavailableSlots = array_merge($bookedSlots, $blockedSlots);
        $availableSlots = array_diff($allSlots, $unavailableSlots);

        // 9. Obter informações de bloqueios para retornar ao frontend
        $activeBlocks = \App\Models\ScheduleBlock::where('is_active', true)
            ->where('date', $date)
            ->get()
            ->map(function ($block) {
                return [
                    'id' => $block->id,
                    'type' => $block->type,
                    'start_time' => $block->start_time ? $block->start_time->format('H:i') : null,
                    'end_time' => $block->end_time ? $block->end_time->format('H:i') : null,
                    'reason' => $block->reason
                ];
            });

        return response()->json([
            'available_slots' => array_values($availableSlots),
            'date' => $date,
            'work_hours' => [
                'start' => $workStartTime,
                'end' => $workEndTime,
                'duration' => $appointmentDuration
            ],
            'active_blocks' => $activeBlocks,
            'total_slots' => count($allSlots),
            'available_count' => count($availableSlots),
            'booked_count' => count($bookedSlots)
        ]);
    }

    /**
     * Obter disponibilidade de múltiplas datas (útil para calendários)
     */
    public function getAvailabilityCalendar(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        // Limitar a 60 dias para performance
        if ($startDate->diffInDays($endDate) > 60) {
            return response()->json([
                'error' => 'Período muito longo. Máximo de 60 dias.'
            ], 400);
        }

        $availability = [];
        $workDays = \App\Models\Setting::get('work_days', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);

        $period = CarbonPeriod::create($startDate, $endDate);
        
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $dayOfWeekLower = strtolower($date->format('l'));
            
            // Verificar se é dia de trabalho
            $isWorkDay = in_array($dayOfWeekLower, $workDays);
            
            // Verificar se está totalmente bloqueado
            $isFullyBlocked = \App\Models\ScheduleBlock::where('is_active', true)
                ->where('date', $dateStr)
                ->where('type', 'full_day')
                ->exists();
            
            // Contar slots disponíveis (versão simplificada para performance)
            $availableCount = 0;
            $totalSlots = 0;
            
            if ($isWorkDay && !$isFullyBlocked) {
                $workStartTime = \App\Models\Setting::get('work_start_time', '09:00');
                $workEndTime = \App\Models\Setting::get('work_end_time', '17:00');
                $appointmentDuration = \App\Models\Setting::get('appointment_duration', 30);
                
                $startHour = Carbon::parse("$dateStr $workStartTime");
                $endHour = Carbon::parse("$dateStr $workEndTime");
                
                // Calcular total de slots possíveis
                $totalSlots = $startHour->diffInMinutes($endHour) / $appointmentDuration;
                
                // Contar agendamentos ocupados
                $bookedCount = Appointment::whereDate('scheduled_at', $dateStr)
                    ->where(function ($query) {
                        $query->where('status', 'confirmed')
                              ->orWhere(function ($query) {
                                  $query->where('status', 'pending')
                                        ->where('created_at', '>', Carbon::now()->subMinutes(5));
                              });
                    })
                    ->count();
                
                // Contar slots bloqueados por horário
                $timeRangeBlocks = \App\Models\ScheduleBlock::where('is_active', true)
                    ->where('date', $dateStr)
                    ->where('type', 'time_range')
                    ->get();
                
                $blockedSlotsCount = 0;
                foreach ($timeRangeBlocks as $block) {
                    $blockStart = Carbon::parse($dateStr . ' ' . $block->start_time);
                    $blockEnd = Carbon::parse($dateStr . ' ' . $block->end_time);
                    $blockedSlotsCount += $blockStart->diffInMinutes($blockEnd) / $appointmentDuration;
                }
                
                $availableCount = max(0, $totalSlots - $bookedCount - $blockedSlotsCount);
            }
            
            $availability[] = [
                'date' => $dateStr,
                'is_work_day' => $isWorkDay,
                'is_fully_blocked' => $isFullyBlocked,
                'available_slots' => (int) $availableCount,
                'total_slots' => (int) $totalSlots,
                'status' => $this->getDateAvailabilityStatus($isWorkDay, $isFullyBlocked, $availableCount)
            ];
        }

        return response()->json([
            'availability' => $availability,
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'total_days' => count($availability)
            ]
        ]);
    }

    /**
     * Determinar status de disponibilidade de uma data
     */
    private function getDateAvailabilityStatus($isWorkDay, $isFullyBlocked, $availableCount)
    {
        if (!$isWorkDay) {
            return 'closed';
        }
        
        if ($isFullyBlocked) {
            return 'blocked';
        }
        
        if ($availableCount == 0) {
            return 'full';
        }
        
        if ($availableCount <= 2) {
            return 'limited';
        }
        
        return 'available';
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
