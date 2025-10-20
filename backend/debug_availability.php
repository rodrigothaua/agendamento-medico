<?php

// Teste para verificar disponibilidade de datas - Debug
// Execute: php debug_availability.php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Setting;
use App\Models\ScheduleBlock;
use Carbon\Carbon;

echo "=== TESTE DE DISPONIBILIDADE DE DATAS ===\n\n";

// Teste para 25/10/2025 (sexta-feira)
$testDate = '2025-10-25';
$date = Carbon::parse($testDate);
$dayOfWeekLower = strtolower($date->format('l'));

echo "Data testada: $testDate\n";
echo "Dia da semana: {$date->format('l')} ($dayOfWeekLower)\n\n";

// Verificar configurações
$workDays = Setting::get('work_days', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
echo "Dias de trabalho configurados: " . json_encode($workDays) . "\n";
$isWorkDay = in_array($dayOfWeekLower, $workDays);
echo "É dia de trabalho? " . ($isWorkDay ? 'SIM' : 'NÃO') . "\n\n";

// Verificar bloqueios
$isFullyBlocked = ScheduleBlock::where('is_active', true)
    ->where('date', $testDate)
    ->where('type', 'full_day')
    ->exists();
echo "Totalmente bloqueado? " . ($isFullyBlocked ? 'SIM' : 'NÃO') . "\n\n";

// Configurações de trabalho
$workStartTime = Setting::get('work_start_time', '09:00');
$workEndTime = Setting::get('work_end_time', '17:00');
$appointmentDuration = Setting::get('appointment_duration', 30);

echo "Horário de trabalho: $workStartTime às $workEndTime\n";
echo "Duração consulta: {$appointmentDuration} minutos\n\n";

if ($isWorkDay && !$isFullyBlocked) {
    $startHour = Carbon::parse("$testDate $workStartTime");
    $endHour = Carbon::parse("$testDate $workEndTime");
    
    // Calcular total de slots possíveis
    $totalSlots = $startHour->diffInMinutes($endHour) / $appointmentDuration;
    echo "Total de slots possíveis: $totalSlots\n";
    
    // Verificar bloqueios de horário
    $timeRangeBlocks = ScheduleBlock::where('is_active', true)
        ->where('date', $testDate)
        ->where('type', 'time_range')
        ->get();
    
    echo "Bloqueios de horário encontrados: " . $timeRangeBlocks->count() . "\n";
    
    $blockedSlotsCount = 0;
    foreach ($timeRangeBlocks as $block) {
        echo "  - Bloqueio: {$block->start_time} às {$block->end_time} ({$block->reason})\n";
        $blockStart = Carbon::parse($testDate . ' ' . $block->start_time);
        $blockEnd = Carbon::parse($testDate . ' ' . $block->end_time);
        $blockSlots = $blockStart->diffInMinutes($blockEnd) / $appointmentDuration;
        $blockedSlotsCount += $blockSlots;
        echo "    Slots bloqueados: $blockSlots\n";
    }
    
    echo "Total de slots bloqueados: $blockedSlotsCount\n";
    
    // Contar agendamentos (assumindo 0 para teste)
    $bookedCount = 0;
    
    $availableCount = max(0, $totalSlots - $bookedCount - $blockedSlotsCount);
    echo "Slots disponíveis: $availableCount\n\n";
    
    // Determinar status
    if ($availableCount == 0) {
        $status = 'full';
    } elseif ($availableCount <= 2) {
        $status = 'limited';
    } else {
        $status = 'available';
    }
    
    echo "STATUS FINAL: $status\n";
} else {
    if (!$isWorkDay) {
        echo "STATUS FINAL: closed (não é dia de trabalho)\n";
    } else {
        echo "STATUS FINAL: blocked (totalmente bloqueado)\n";
    }
}

echo "\n=== FIM DO TESTE ===\n";