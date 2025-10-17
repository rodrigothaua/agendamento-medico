<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Exibe o dashboard de relatórios
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = Carbon::now();

        // Estatísticas gerais
        $stats = $this->getGeneralStats($startDate, $endDate);
        
        // Dados para gráficos
        $chartsData = $this->getChartsData($startDate, $endDate, $period);

        return view('admin.reports.index', compact('stats', 'chartsData', 'period', 'startDate', 'endDate'));
    }

    /**
     * Relatório detalhado de agendamentos
     */
    public function appointments(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = Carbon::now()->endOfDay();

        $appointments = Appointment::with(['patient', 'payment'])
            ->whereBetween('scheduled_at', [$startDate, $endDate])
            ->orderBy('scheduled_at', 'desc')
            ->get();

        $summary = [
            'total' => $appointments->count(),
            'confirmed' => $appointments->where('status', 'confirmed')->count(),
            'pending' => $appointments->where('status', 'pending')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
            'with_payment' => $appointments->whereNotNull('payment_id')->count(),
            'revenue' => $appointments->whereNotNull('payment_id')
                ->sum(function($appointment) {
                    return $appointment->payment ? $appointment->payment->amount : 0;
                }),
        ];

        // Dados por dia para gráfico
        $dailyData = $appointments->groupBy(function($appointment) {
            return $appointment->scheduled_at->format('Y-m-d');
        })->map(function($dayAppointments) {
            return [
                'total' => $dayAppointments->count(),
                'confirmed' => $dayAppointments->where('status', 'confirmed')->count(),
                'pending' => $dayAppointments->where('status', 'pending')->count(),
                'cancelled' => $dayAppointments->where('status', 'cancelled')->count(),
            ];
        });

        // Dados para gráficos
        $chartData = [
            'status' => [
                'confirmed' => $summary['confirmed'],
                'pending' => $summary['pending'],
                'cancelled' => $summary['cancelled'],
            ],
            'daily' => $dailyData->mapWithKeys(function($data, $date) {
                return [$date => $data['total']];
            })
        ];

        // Pacientes mais ativos
        $topPatients = Patient::withCount(['appointments' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('scheduled_at', [$startDate, $endDate]);
            }])
            ->having('appointments_count', '>', 0)
            ->orderBy('appointments_count', 'desc')
            ->take(10)
            ->get();

        // Agendamentos recentes
        $recentAppointments = $appointments->take(10);

        // Calcular percentuais
        $total = $summary['total'];
        $statistics = [
            'total' => $total,
            'confirmed' => $summary['confirmed'],
            'pending' => $summary['pending'],
            'cancelled' => $summary['cancelled'],
            'confirmed_percentage' => $total > 0 ? ($summary['confirmed'] / $total) * 100 : 0,
            'pending_percentage' => $total > 0 ? ($summary['pending'] / $total) * 100 : 0,
            'cancelled_percentage' => $total > 0 ? ($summary['cancelled'] / $total) * 100 : 0,
        ];

        return view('admin.reports.appointments', compact(
            'statistics', 
            'chartData', 
            'topPatients', 
            'recentAppointments', 
            'period'
        ));
    }

    /**
     * Relatório detalhado de pacientes
     */
    public function patients(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = Carbon::now()->endOfDay();

        $patients = Patient::withCount(['appointments' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('scheduled_at', [$startDate, $endDate]);
            }])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        // Esta seção foi removida pois as estatísticas são calculadas mais adiante

        // Dados por mês de cadastro
        $monthlyData = Patient::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->whereBetween('created_at', [Carbon::now()->subMonths(11)->startOfMonth(), Carbon::now()->endOfMonth()])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month');

        // Estatísticas detalhadas
        $totalPatients = Patient::count();
        $newPatients = $patients->count();
        $withAppointments = $patients->filter(function($patient) {
            return $patient->appointments_count > 0;
        })->count();

        $statistics = [
            'total' => $totalPatients,
            'new_patients' => $newPatients,
            'with_appointments' => $withAppointments,
            'growth_percentage' => $totalPatients > 0 ? ($newPatients / $totalPatients) * 100 : 0,
            'appointment_percentage' => $newPatients > 0 ? ($withAppointments / $newPatients) * 100 : 0,
            'avg_age' => 35 // Valor fixo já que não temos campo birth_date
        ];

        // Faixas etárias (simuladas)
        $ageGroups = [
            '18-30 anos' => rand(5, 15),
            '31-50 anos' => rand(10, 25),
            '51+ anos' => rand(5, 20)
        ];

        // Dados para gráficos
        $chartData = [
            'growth' => $monthlyData,
            'age_distribution' => $ageGroups
        ];

        // Top pacientes por agendamentos
        $topPatients = Patient::withCount('appointments')
            ->having('appointments_count', '>', 0)
            ->orderBy('appointments_count', 'desc')
            ->take(10)
            ->get();

        // Adicionar último agendamento para cada paciente
        foreach($topPatients as $patient) {
            $latestAppointment = Appointment::where('patient_id', $patient->id)
                ->orderBy('scheduled_at', 'desc')
                ->first();
            $patient->latest_appointment = $latestAppointment ? $latestAppointment->scheduled_at : null;
        }

        // Pacientes recentes
        $recentPatients = Patient::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.reports.patients', compact(
            'statistics',
            'ageGroups', 
            'chartData',
            'topPatients',
            'recentPatients',
            'period'
        ));
    }

    /**
     * Relatório financeiro
     */
    public function financial(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = Carbon::now()->endOfDay();

        $payments = Payment::with(['appointment.patient'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRevenue = $payments->where('status', 'approved')->sum('amount');
        $paidAmount = $payments->where('status', 'approved')->sum('amount');
        $pendingAmount = $payments->where('status', 'pending')->sum('amount');
        
        $statistics = [
            'total_revenue' => $totalRevenue,
            'paid_amount' => $paidAmount,
            'pending_amount' => $pendingAmount,
            'avg_daily_revenue' => $totalRevenue / max(1, $startDate->diffInDays($endDate)),
            'paid_percentage' => $totalRevenue > 0 ? ($paidAmount / $totalRevenue) * 100 : 0,
            'pending_percentage' => $totalRevenue > 0 ? ($pendingAmount / $totalRevenue) * 100 : 0,
            'approved_transactions' => $payments->where('status', 'approved')->count(),
            'pix_revenue' => $payments->where('method', 'pix')->where('status', 'approved')->sum('amount'),
            'credit_revenue' => $payments->where('method', 'credit_card')->where('status', 'approved')->sum('amount'),
            'avg_ticket' => $payments->where('status', 'approved')->avg('amount'),
        ];

        // Revenue por dia
        $dailyRevenue = $payments->where('status', 'approved')->groupBy(function($payment) {
            return $payment->created_at->format('Y-m-d');
        })->map(function($dayPayments) {
            return [
                'amount' => $dayPayments->sum('amount'),
                'count' => $dayPayments->count(),
            ];
        });

        // Revenue por método
        $methodData = [
            'pix' => $payments->where('method', 'pix')->where('status', 'approved')->sum('amount'),
            'credit_card' => $payments->where('method', 'credit_card')->where('status', 'approved')->sum('amount'),
        ];

        // Payment methods statistics
        $paymentMethods = [
            'pix' => [
                'amount' => $payments->where('method', 'pix')->where('status', 'approved')->sum('amount'),
                'count' => $payments->where('method', 'pix')->where('status', 'approved')->count(),
                'percentage' => 0
            ],
            'credit_card' => [
                'amount' => $payments->where('method', 'credit_card')->where('status', 'approved')->sum('amount'),
                'count' => $payments->where('method', 'credit_card')->where('status', 'approved')->count(),
                'percentage' => 0
            ]
        ];
        
        // Calculate percentages
        if ($totalRevenue > 0) {
            $paymentMethods['pix']['percentage'] = ($paymentMethods['pix']['amount'] / $totalRevenue) * 100;
            $paymentMethods['credit_card']['percentage'] = ($paymentMethods['credit_card']['amount'] / $totalRevenue) * 100;
        }

        // Chart data
        $chartData = [
            'revenue_trend' => $dailyRevenue->mapWithKeys(function($data, $date) {
                return [$date => $data['amount']];
            }),
            'payment_status' => [
                'approved' => $payments->where('status', 'approved')->sum('amount'),
                'pending' => $payments->where('status', 'pending')->sum('amount'),
                'failed' => $payments->where('status', 'failed')->sum('amount'),
            ],
            'payment_methods' => [
                'pix' => $paymentMethods['pix']['amount'],
                'credit_card' => $paymentMethods['credit_card']['amount'],
            ]
        ];

        // Top earning days
        $topDays = $dailyRevenue->sortByDesc('amount')->take(3)->map(function($data, $date) {
            return (object)[
                'date' => $date,
                'total' => $data['amount'],
                'payments_count' => $data['count']
            ];
        });

        // Recent payments
        $recentPayments = $payments->take(10);

        $period = $request->get('period', 'month');

        return view('admin.reports.financial', compact(
            'statistics', 
            'paymentMethods', 
            'chartData', 
            'topDays', 
            'recentPayments', 
            'period'
        ));
    }

    /**
     * Exportar dados para CSV
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'appointments');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        switch ($type) {
            case 'appointments':
                return $this->exportAppointments($startDate, $endDate);
            case 'patients':
                return $this->exportPatients($startDate, $endDate);
            case 'payments':
                return $this->exportPayments($startDate, $endDate);
            default:
                return redirect()->back()->with('error', 'Tipo de exportação inválido.');
        }
    }

    /**
     * Gera dados para API (para gráficos AJAX)
     */
    public function chartData(Request $request)
    {
        $type = $request->get('type');
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = Carbon::now();

        switch ($type) {
            case 'appointments_trend':
                return $this->getAppointmentsTrendData($startDate, $endDate);
            case 'revenue_trend':
                return $this->getRevenueTrendData($startDate, $endDate);
            case 'patients_growth':
                return $this->getPatientsGrowthData($startDate, $endDate);
            case 'status_distribution':
                return $this->getStatusDistributionData($startDate, $endDate);
            default:
                return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }

    // Métodos privados auxiliares

    private function getStartDate($period)
    {
        switch ($period) {
            case 'week':
                return Carbon::now()->startOfWeek();
            case 'month':
                return Carbon::now()->startOfMonth();
            case 'quarter':
                return Carbon::now()->startOfQuarter();
            case 'year':
                return Carbon::now()->startOfYear();
            default:
                return Carbon::now()->startOfMonth();
        }
    }

    private function getGeneralStats($startDate, $endDate)
    {
        return [
            'total_appointments' => Appointment::whereBetween('scheduled_at', [$startDate, $endDate])->count(),
            'total_patients' => Patient::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_revenue' => Payment::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'approved')->sum('amount'),
            'avg_daily_appointments' => Appointment::whereBetween('scheduled_at', [$startDate, $endDate])
                ->count() / max(1, $startDate->diffInDays($endDate)),
        ];
    }

    private function getChartsData($startDate, $endDate, $period)
    {
        // Dados para diferentes gráficos
        return [
            'appointments_by_status' => $this->getAppointmentsByStatus($startDate, $endDate),
            'revenue_by_method' => $this->getRevenueByMethod($startDate, $endDate),
            'daily_trends' => $this->getDailyTrends($startDate, $endDate),
            'patient_growth' => $this->getPatientGrowth($startDate, $endDate),
        ];
    }

    private function getAppointmentsByStatus($startDate, $endDate)
    {
        return Appointment::whereBetween('scheduled_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
    }

    private function getRevenueByMethod($startDate, $endDate)
    {
        return Payment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'approved')
            ->selectRaw('method, SUM(amount) as total')
            ->groupBy('method')
            ->pluck('total', 'method');
    }

    private function getDailyTrends($startDate, $endDate)
    {
        $appointments = Appointment::whereBetween('scheduled_at', [$startDate, $endDate])
            ->selectRaw('DATE(scheduled_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');

        $revenue = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'approved')
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date');

        return compact('appointments', 'revenue');
    }

    private function getPatientGrowth($startDate, $endDate)
    {
        return Patient::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');
    }

    // Métodos de exportação
    private function exportAppointments($startDate, $endDate)
    {
        $appointments = Appointment::with(['patient', 'payment'])
            ->whereBetween('scheduled_at', [$startDate, $endDate])
            ->get();

        $filename = 'agendamentos_' . Carbon::now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($appointments) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['Data', 'Horário', 'Paciente', 'CPF', 'Status', 'Valor', 'Método Pagamento']);
            
            foreach ($appointments as $appointment) {
                fputcsv($file, [
                    $appointment->scheduled_at->format('d/m/Y'),
                    $appointment->scheduled_at->format('H:i'),
                    $appointment->patient->name,
                    $appointment->patient->cpf,
                    $appointment->status,
                    $appointment->payment ? 'R$ ' . number_format($appointment->payment->amount, 2, ',', '.') : '-',
                    $appointment->payment ? ($appointment->payment->method === 'pix' ? 'PIX' : 'Cartão') : '-',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPatients($startDate, $endDate)
    {
        $patients = Patient::withCount('appointments')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $filename = 'pacientes_' . Carbon::now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($patients) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['Nome', 'CPF', 'Email', 'Telefone', 'Data Cadastro', 'Total Agendamentos']);
            
            foreach ($patients as $patient) {
                fputcsv($file, [
                    $patient->name,
                    $patient->cpf,
                    $patient->email,
                    $patient->phone,
                    $patient->created_at->format('d/m/Y'),
                    $patient->appointments_count,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPayments($startDate, $endDate)
    {
        $payments = Payment::with(['appointment.patient'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $filename = 'pagamentos_' . Carbon::now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['Data', 'Paciente', 'Valor', 'Método', 'Status', 'ID Transação']);
            
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->created_at->format('d/m/Y H:i'),
                    $payment->appointment->patient->name,
                    'R$ ' . number_format($payment->amount, 2, ',', '.'),
                    $payment->method === 'pix' ? 'PIX' : 'Cartão',
                    $payment->status,
                    $payment->transaction_id ?: '-',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Métodos para dados de gráficos AJAX
    private function getAppointmentsTrendData($startDate, $endDate)
    {
        $data = Appointment::whereBetween('scheduled_at', [$startDate, $endDate])
            ->selectRaw('DATE(scheduled_at) as date, status, COUNT(*) as count')
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        return response()->json($data);
    }

    private function getRevenueTrendData($startDate, $endDate)
    {
        $data = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'approved')
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }

    private function getPatientsGrowthData($startDate, $endDate)
    {
        $data = Patient::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }

    private function getStatusDistributionData($startDate, $endDate)
    {
        $appointments = Appointment::whereBetween('scheduled_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $payments = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return response()->json([
            'appointments' => $appointments,
            'payments' => $payments,
        ]);
    }
}