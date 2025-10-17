<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Exibe a lista de pagamentos com filtros
     */
    public function index(Request $request)
    {
        $query = Payment::with(['appointment.patient']);

        // Filtro por status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filtro por método de pagamento
        if ($request->filled('method') && $request->method !== 'all') {
            $query->where('method', $request->method);
        }

        // Filtro por período
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'month':
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth()
                    ]);
                    break;
                case 'last_month':
                    $query->whereBetween('created_at', [
                        Carbon::now()->subMonth()->startOfMonth(),
                        Carbon::now()->subMonth()->endOfMonth()
                    ]);
                    break;
            }
        }

        // Filtro por busca (paciente ou ID transação)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('appointment.patient', function ($patient) use ($search) {
                      $patient->where('name', 'like', "%{$search}%")
                             ->orWhere('cpf', 'like', "%{$search}%");
                  });
            });
        }

        // Ordenação
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSorts = ['created_at', 'amount', 'status', 'paid_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $payments = $query->paginate(15);

        // Estatísticas
        $stats = $this->getPaymentStats();

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Exibe os detalhes de um pagamento específico
     */
    public function show($id)
    {
        $payment = Payment::with(['appointment.patient'])->findOrFail($id);
        
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Atualiza o status de um pagamento
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,failed',
        ]);

        $payment = Payment::findOrFail($id);
        $oldStatus = $payment->status;
        
        $payment->update([
            'status' => $request->status,
            'paid_at' => $request->status === 'approved' ? Carbon::now() : null,
        ]);

        // Se aprovado, confirmar o agendamento automaticamente
        if ($request->status === 'approved' && $payment->appointment) {
            $payment->appointment->update(['status' => 'confirmed']);
        }

        $statusText = [
            'pending' => 'Pendente',
            'approved' => 'Aprovado',
            'failed' => 'Rejeitado'
        ];

        return redirect()->back()->with('success', 
            "Status do pagamento alterado de '{$statusText[$oldStatus]}' para '{$statusText[$request->status]}' com sucesso!"
        );
    }

    /**
     * Exclui um pagamento
     */
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        
        // Verificar se pode excluir
        if ($payment->status === 'approved') {
            return redirect()->back()->with('error', 
                'Não é possível excluir um pagamento que já foi aprovado.'
            );
        }

        // Se houver agendamento vinculado, voltar status para pendente
        if ($payment->appointment) {
            $payment->appointment->update(['status' => 'pending']);
        }

        $payment->delete();

        return redirect()->route('admin.payments.index')->with('success', 
            'Pagamento excluído com sucesso!'
        );
    }

    /**
     * Gera relatório de pagamentos
     */
    public function report(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $payments = Payment::with(['appointment.patient'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_payments' => $payments->count(),
            'total_amount' => $payments->sum('amount'),
            'approved_count' => $payments->where('status', 'approved')->count(),
            'approved_amount' => $payments->where('status', 'approved')->sum('amount'),
            'pending_count' => $payments->where('status', 'pending')->count(),
            'pending_amount' => $payments->where('status', 'pending')->sum('amount'),
            'failed_count' => $payments->where('status', 'failed')->count(),
            'failed_amount' => $payments->where('status', 'failed')->sum('amount'),
            'pix_count' => $payments->where('method', 'pix')->count(),
            'pix_amount' => $payments->where('method', 'pix')->sum('amount'),
            'credit_count' => $payments->where('method', 'credit_card')->count(),
            'credit_amount' => $payments->where('method', 'credit_card')->sum('amount'),
        ];

        return view('admin.payments.report', compact('payments', 'summary', 'startDate', 'endDate'));
    }

    /**
     * Calcula estatísticas de pagamentos
     */
    private function getPaymentStats()
    {
        $total = Payment::count();
        $totalAmount = Payment::sum('amount');
        
        $approved = Payment::where('status', 'approved')->count();
        $approvedAmount = Payment::where('status', 'approved')->sum('amount');
        
        $pending = Payment::where('status', 'pending')->count();
        $pendingAmount = Payment::where('status', 'pending')->sum('amount');
        
        $failed = Payment::where('status', 'failed')->count();
        
        $todayPayments = Payment::whereDate('created_at', Carbon::today())->count();
        $todayAmount = Payment::whereDate('created_at', Carbon::today())->sum('amount');
        
        $monthPayments = Payment::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->count();
        
        $monthAmount = Payment::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->sum('amount');

        return [
            'total' => $total,
            'total_amount' => $totalAmount,
            'approved' => $approved,
            'approved_amount' => $approvedAmount,
            'pending' => $pending,
            'pending_amount' => $pendingAmount,
            'failed' => $failed,
            'today_payments' => $todayPayments,
            'today_amount' => $todayAmount,
            'month_payments' => $monthPayments,
            'month_amount' => $monthAmount,
        ];
    }
}