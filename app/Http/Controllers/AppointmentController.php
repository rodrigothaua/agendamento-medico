<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Exibe a lista de agendamentos com filtros
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'payment']);

        // Filtro por status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filtro por data
        if ($request->filled('date')) {
            $query->whereDate('scheduled_at', $request->date);
        }

        // Filtro por período
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('scheduled_at', Carbon::today());
                    break;
                case 'tomorrow':
                    $query->whereDate('scheduled_at', Carbon::tomorrow());
                    break;
                case 'week':
                    $query->whereBetween('scheduled_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'month':
                    $query->whereBetween('scheduled_at', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth()
                    ]);
                    break;
            }
        }

        // Filtro por nome do paciente
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Ordenação
        $sortBy = $request->get('sort', 'scheduled_at');
        $sortOrder = $request->get('order', 'desc');
        
        if (in_array($sortBy, ['scheduled_at', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('scheduled_at', 'desc');
        }

        $appointments = $query->paginate(15)->withQueryString();

        // Estatísticas para filtros
        $stats = [
            'total' => Appointment::count(),
            'confirmed' => Appointment::where('status', 'confirmed')->count(),
            'pending' => Appointment::where('status', 'pending')->count(),
            'canceled' => Appointment::where('status', 'canceled')->count(),
        ];

        return view('admin.appointments.index', compact('appointments', 'stats'));
    }

    /**
     * Atualiza o status de um agendamento
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,canceled'
        ]);

        $appointment = Appointment::findOrFail($id);
        $oldStatus = $appointment->status;
        $newStatus = $request->status;

        // Validações de negócio
        if ($oldStatus === 'canceled' && $newStatus !== 'canceled') {
            return back()->with('error', 'Não é possível alterar o status de um agendamento cancelado.');
        }

        // Se está confirmando um agendamento pendente, verificar disponibilidade do horário
        if ($oldStatus === 'pending' && $newStatus === 'confirmed') {
            $conflictingAppointment = Appointment::where('scheduled_at', $appointment->scheduled_at)
                ->where('status', 'confirmed')
                ->where('id', '!=', $appointment->id)
                ->first();

            if ($conflictingAppointment) {
                return back()->with('error', 'Este horário já está ocupado por outro agendamento confirmado.');
            }
        }

        $appointment->status = $newStatus;
        $appointment->save();

        // Atualizar status do pagamento se necessário
        if ($appointment->payment) {
            if ($newStatus === 'confirmed' && $appointment->payment->status === 'pending') {
                $appointment->payment->status = 'approved';
                $appointment->payment->paid_at = now();
                $appointment->payment->save();
            } elseif ($newStatus === 'canceled' && $appointment->payment->status === 'approved') {
                $appointment->payment->status = 'refunded';
                $appointment->payment->save();
            }
        }

        $statusLabels = [
            'pending' => 'Pendente',
            'confirmed' => 'Confirmado',
            'canceled' => 'Cancelado'
        ];

        return back()->with('success', "Agendamento alterado para: {$statusLabels[$newStatus]}");
    }

    /**
     * Exclui um agendamento
     */
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        
        // Verificar se pode ser excluído
        if ($appointment->status === 'confirmed') {
            return back()->with('error', 'Não é possível excluir um agendamento confirmado. Cancele-o primeiro.');
        }

        // Se houver pagamento aprovado, não permitir exclusão
        if ($appointment->payment && $appointment->payment->status === 'approved') {
            return back()->with('error', 'Não é possível excluir um agendamento com pagamento aprovado.');
        }

        $patientName = $appointment->patient->name ?? 'Paciente desconhecido';
        $scheduledAt = Carbon::parse($appointment->scheduled_at)->format('d/m/Y H:i');

        $appointment->delete();

        return back()->with('success', "Agendamento de {$patientName} para {$scheduledAt} foi excluído com sucesso.");
    }

    /**
     * Exibe detalhes de um agendamento
     */
    public function show($id)
    {
        $appointment = Appointment::with(['patient', 'payment'])->findOrFail($id);
        
        return view('admin.appointments.show', compact('appointment'));
    }
}