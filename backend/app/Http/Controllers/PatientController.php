<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    /**
     * Exibe a lista de pacientes com filtros
     */
    public function index(Request $request)
    {
        $query = Patient::withCount(['appointments']);

        // Filtro por busca (nome, CPF, email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por período de cadastro
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', now());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]);
                    break;
                case 'month':
                    $query->whereBetween('created_at', [
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    ]);
                    break;
            }
        }

        // Ordenação
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        if (in_array($sortBy, ['name', 'created_at', 'appointments_count'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $patients = $query->paginate(15)->withQueryString();

        // Estatísticas
        $stats = [
            'total' => Patient::count(),
            'new_today' => Patient::whereDate('created_at', now())->count(),
            'new_week' => Patient::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'with_appointments' => Patient::whereHas('appointments')->count(),
        ];

        return view('admin.patients.index', compact('patients', 'stats'));
    }

    /**
     * Mostra o formulário de criação
     */
    public function create()
    {
        return view('admin.patients.create');
    }

    /**
     * Armazena um novo paciente
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:patients,email',
            'phone' => 'nullable|string|max:20',
            'cpf' => 'required|string|size:11|unique:patients,cpf',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ter um formato válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.size' => 'O CPF deve ter 11 dígitos.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
        ]);

        Patient::create($request->all());

        return redirect()->route('admin.patients.index')
            ->with('success', 'Paciente cadastrado com sucesso!');
    }

    /**
     * Exibe detalhes de um paciente
     */
    public function show($id)
    {
        $patient = Patient::with(['appointments.payment'])->findOrFail($id);
        
        // Estatísticas do paciente
        $patientStats = [
            'total_appointments' => $patient->appointments->count(),
            'confirmed_appointments' => $patient->appointments->where('status', 'confirmed')->count(),
            'pending_appointments' => $patient->appointments->where('status', 'pending')->count(),
            'canceled_appointments' => $patient->appointments->where('status', 'canceled')->count(),
            'total_paid' => $patient->appointments->whereHas('payment', function($q) {
                $q->where('status', 'approved');
            })->sum(function($appointment) {
                return $appointment->payment->amount ?? 0;
            }),
        ];

        return view('admin.patients.show', compact('patient', 'patientStats'));
    }

    /**
     * Mostra o formulário de edição
     */
    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        return view('admin.patients.edit', compact('patient'));
    }

    /**
     * Atualiza os dados do paciente
     */
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('patients')->ignore($patient->id)],
            'phone' => 'nullable|string|max:20',
            'cpf' => ['required', 'string', 'size:11', Rule::unique('patients')->ignore($patient->id)],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ter um formato válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.size' => 'O CPF deve ter 11 dígitos.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
        ]);

        $patient->update($request->all());

        return redirect()->route('admin.patients.show', $patient->id)
            ->with('success', 'Paciente atualizado com sucesso!');
    }

    /**
     * Exclui um paciente
     */
    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);

        // Verificar se tem agendamentos confirmados ou com pagamento aprovado
        $hasConfirmedAppointments = $patient->appointments()
            ->where('status', 'confirmed')
            ->exists();

        $hasPaidAppointments = $patient->appointments()
            ->whereHas('payment', function($q) {
                $q->where('status', 'approved');
            })
            ->exists();

        if ($hasConfirmedAppointments || $hasPaidAppointments) {
            return back()->with('error', 'Não é possível excluir paciente com agendamentos confirmados ou pagamentos aprovados.');
        }

        $patientName = $patient->name;
        $patient->delete();

        return redirect()->route('admin.patients.index')
            ->with('success', "Paciente {$patientName} foi excluído com sucesso.");
    }
}