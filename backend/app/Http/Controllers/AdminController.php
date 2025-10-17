<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Exibe o Dashboard administrativo com estatísticas e agendamentos.
     */
    public function dashboard()
    {
        // 1. Estatísticas Rápidas
        $confirmedAppointments = Appointment::where('status', 'confirmed')->count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();
        $totalPatients = Patient::count();
        $todayConfirmed = Appointment::where('status', 'confirmed')
            ->whereDate('scheduled_at', Carbon::today())
            ->count();
        
        // Estatísticas de pacientes
        $newPatientsThisWeek = Patient::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();

        // 2. Agendamentos Recentes (ou Próximos)
        $appointments = Appointment::with(['patient', 'payment'])
            ->orderBy('scheduled_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', [
            'stats' => [
                'confirmed' => $confirmedAppointments,
                'pending' => $pendingAppointments,
                'patients' => $totalPatients,
                'today_confirmed' => $todayConfirmed,
                'new_patients_week' => $newPatientsThisWeek,
            ],
            'appointments' => $appointments,
        ]);
    }
}
