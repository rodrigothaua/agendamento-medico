<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $patient->name }} - Clínica Saúde</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f9;
        }
        .card {
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease-in-out;
        }
    </style>
</head>
<body>

<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center space-x-4">
                    <div class="h-16 w-16 bg-indigo-500 rounded-full flex items-center justify-center">
                        <span class="text-2xl font-bold text-white">{{ substr($patient->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $patient->name }}</h1>
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span>CPF: {{ $patient->cpf }}</span>
                            <span>•</span>
                            <span>Cadastrado em {{ $patient->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 md:mt-0 flex space-x-3">
                    <a href="{{ route('admin.patients.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                    <a href="{{ route('admin.patients.edit', $patient->id) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information -->
                <div class="bg-white card p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Dados Pessoais</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nome Completo</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $patient->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">CPF</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $patient->cpf }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Data de Nascimento</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($patient->date_of_birth)
                                    {{ $patient->date_of_birth->format('d/m/Y') }}
                                    <span class="text-gray-500">
                                        ({{ $patient->date_of_birth->age }} anos)
                                    </span>
                                @else
                                    Não informado
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Gênero</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @switch($patient->gender)
                                    @case('M') Masculino @break
                                    @case('F') Feminino @break
                                    @case('O') Outro @break
                                    @default Não informado
                                @endswitch
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-white card p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Contato</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">E-mail</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <a href="mailto:{{ $patient->email }}" class="text-indigo-600 hover:text-indigo-800">
                                    {{ $patient->email }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Telefone</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($patient->phone)
                                    <a href="tel:{{ $patient->phone }}" class="text-indigo-600 hover:text-indigo-800">
                                        {{ $patient->phone }}
                                    </a>
                                @else
                                    Não informado
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Address -->
                @if($patient->address || $patient->city || $patient->state || $patient->zip_code)
                    <div class="bg-white card p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Endereço</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">CEP</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $patient->zip_code ?: 'Não informado' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500">Endereço</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $patient->address ?: 'Não informado' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Cidade</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $patient->city ?: 'Não informado' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Estado</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $patient->state ?: 'Não informado' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">País</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $patient->country ?: 'Não informado' }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Medical Information -->
                @if($patient->allergies || $patient->medical_history || $patient->emergency_contact)
                    <div class="bg-white card p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informações Médicas</h3>
                        <div class="space-y-4">
                            @if($patient->allergies)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Alergias</label>
                                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $patient->allergies }}</p>
                                </div>
                            @endif
                            @if($patient->medical_history)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Histórico Médico</label>
                                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $patient->medical_history }}</p>
                                </div>
                            @endif
                            @if($patient->emergency_contact)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Contato de Emergência</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $patient->emergency_contact }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Notes -->
                @if($patient->notes)
                    <div class="bg-white card p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Observações</h3>
                        <p class="text-sm text-gray-900 whitespace-pre-line">{{ $patient->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white card p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ações Rápidas</h3>
                    <div class="space-y-3">
                        <button onclick="window.print()" 
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Imprimir Ficha
                        </button>

                        <a href="{{ route('admin.appointments.create', ['patient_id' => $patient->id]) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Novo Agendamento
                        </a>

                        <button onclick="confirmDelete({{ $patient->id }}, '{{ $patient->name }}')" 
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Excluir Paciente
                        </button>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="bg-white card p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Estatísticas</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Total de Agendamentos</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $patient->appointments()->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Confirmados</span>
                            <span class="text-lg font-semibold text-green-600">
                                {{ $patient->appointments()->where('status', 'confirmed')->count() }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Pendentes</span>
                            <span class="text-lg font-semibold text-yellow-600">
                                {{ $patient->appointments()->where('status', 'pending')->count() }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Cancelados</span>
                            <span class="text-lg font-semibold text-red-600">
                                {{ $patient->appointments()->where('status', 'cancelled')->count() }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Recent Appointments -->
                @if($patient->appointments()->latest()->limit(5)->count() > 0)
                    <div class="bg-white card p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Agendamentos Recentes</h3>
                        <div class="space-y-3">
                            @foreach($patient->appointments()->latest()->limit(5)->get() as $appointment)
                                <div class="border-l-4 @if($appointment->status == 'confirmed') border-green-400 @elseif($appointment->status == 'pending') border-yellow-400 @else border-red-400 @endif pl-3 py-2">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $appointment->scheduled_at->format('d/m/Y') }} às 
                                        {{ $appointment->scheduled_at->format('H:i') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Status: 
                                        @switch($appointment->status)
                                            @case('pending') <span class="text-yellow-600">Pendente</span> @break
                                            @case('confirmed') <span class="text-green-600">Confirmado</span> @break
                                            @case('cancelled') <span class="text-red-600">Cancelado</span> @break
                                        @endswitch
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.appointments.index', ['patient_id' => $patient->id]) }}" 
                               class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                Ver todos os agendamentos →
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Form para exclusão -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDelete(patientId, patientName) {
    if (confirm(`Tem certeza que deseja excluir o paciente ${patientName}?\n\nEsta ação não pode ser desfeita e todos os agendamentos relacionados também serão removidos.`)) {
        const form = document.getElementById('delete-form');
        form.action = `{{ route('admin.patients.index') }}/${patientId}`;
        form.submit();
    }
}

// Print styles
window.addEventListener('beforeprint', function() {
    document.querySelectorAll('.no-print').forEach(el => el.style.display = 'none');
});

window.addEventListener('afterprint', function() {
    document.querySelectorAll('.no-print').forEach(el => el.style.display = '');
});
</script>

<style media="print">
    .no-print {
        display: none !important;
    }
    
    body {
        background: white !important;
        font-size: 12px;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #e5e7eb;
        page-break-inside: avoid;
    }
    
    .grid {
        display: block !important;
    }
    
    .lg\:col-span-2 {
        width: 100% !important;
    }
    
    .space-y-6 > * + * {
        margin-top: 1rem !important;
    }
</style>

</body>
</html>