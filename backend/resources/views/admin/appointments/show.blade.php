<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Agendamento - Clínica Saúde</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f9;
        }
    </style>
</head>
<body class="bg-gray-50">

<div class="min-h-screen py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detalhes do Agendamento</h1>
                    <p class="text-sm text-gray-600">Informações completas do agendamento</p>
                </div>
                <a href="{{ route('admin.appointments.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Voltar
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informações do Agendamento -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Informações do Agendamento</h3>
                        
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID</dt>
                                <dd class="mt-1 text-sm text-gray-900">#{{ $appointment->id }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    @php
                                        $statusConfig = [
                                            'confirmed' => ['class' => 'bg-green-100 text-green-800', 'text' => 'Confirmado'],
                                            'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'text' => 'Pendente'],
                                            'canceled' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Cancelado'],
                                        ];
                                        $config = $statusConfig[$appointment->status] ?? ['class' => 'bg-gray-100 text-gray-800', 'text' => ucfirst($appointment->status)];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $config['class'] }}">
                                        {{ $config['text'] }}
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Data</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d/m/Y') }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Horário</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('H:i') }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Criado em</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $appointment->created_at->format('d/m/Y H:i') }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Atualizado em</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $appointment->updated_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Informações do Paciente -->
                <div class="bg-white shadow rounded-lg mt-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Informações do Paciente</h3>
                        
                        <div class="flex items-center mb-4">
                            <div class="h-16 w-16 rounded-full bg-indigo-500 flex items-center justify-center">
                                <span class="text-xl font-medium text-white">
                                    {{ substr($appointment->patient->name ?? 'N', 0, 1) }}
                                </span>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-gray-900">{{ $appointment->patient->name ?? 'N/A' }}</h4>
                                <p class="text-sm text-gray-500">Paciente</p>
                            </div>
                        </div>

                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">CPF</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $appointment->patient->cpf ?? 'N/A' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">E-mail</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $appointment->patient->email ?? 'N/A' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Telefone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $appointment->patient->phone ?? 'N/A' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Paciente desde</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $appointment->patient->created_at->format('d/m/Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Ações -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Ações</h3>
                        
                        <div class="space-y-3">
                            @if($appointment->status !== 'canceled')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Alterar Status</label>
                                    <select onchange="updateStatus({{ $appointment->id }}, this.value)" 
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Selecione um status</option>
                                        @if($appointment->status === 'pending')
                                            <option value="confirmed">Confirmar</option>
                                        @endif
                                        @if($appointment->status !== 'canceled')
                                            <option value="canceled">Cancelar</option>
                                        @endif
                                    </select>
                                </div>
                            @endif

                            @if($appointment->status !== 'confirmed' || ($appointment->payment && $appointment->payment->status !== 'approved'))
                                <button onclick="confirmDelete({{ $appointment->id }}, '{{ $appointment->patient->name ?? 'Paciente' }}', '{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d/m/Y H:i') }}')" 
                                        class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-200">
                                    Excluir Agendamento
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Informações do Pagamento -->
                @if($appointment->payment)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pagamento</h3>
                        
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Valor</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">R$ {{ number_format($appointment->payment->amount, 2, ',', '.') }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    @php
                                        $paymentConfig = [
                                            'approved' => ['class' => 'bg-green-100 text-green-800', 'text' => 'Pago'],
                                            'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'text' => 'Pendente'],
                                            'failed' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Falhou'],
                                            'refunded' => ['class' => 'bg-gray-100 text-gray-800', 'text' => 'Estornado'],
                                        ];
                                        $payConfig = $paymentConfig[$appointment->payment->status] ?? ['class' => 'bg-gray-100 text-gray-800', 'text' => ucfirst($appointment->payment->status)];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $payConfig['class'] }}">
                                        {{ $payConfig['text'] }}
                                    </span>
                                </dd>
                            </div>

                            @if($appointment->payment->method)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Método</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $appointment->payment->method === 'pix' ? 'PIX' : 'Cartão de Crédito' }}
                                </dd>
                            </div>
                            @endif

                            @if($appointment->payment->transaction_id)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID da Transação</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $appointment->payment->transaction_id }}</dd>
                            </div>
                            @endif

                            @if($appointment->payment->paid_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pago em</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($appointment->payment->paid_at)->format('d/m/Y H:i') }}</dd>
                            </div>
                            @endif

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Criado em</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $appointment->payment->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
                @else
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pagamento</h3>
                        <p class="text-sm text-gray-500">Nenhum pagamento associado a este agendamento.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Forms para ações -->
<form id="status-form" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" id="status-input">
</form>

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function updateStatus(appointmentId, status) {
    if (!status) return;
    
    const statusTexts = {
        'confirmed': 'confirmar',
        'canceled': 'cancelar'
    };
    
    if (confirm(`Tem certeza que deseja ${statusTexts[status]} este agendamento?`)) {
        const form = document.getElementById('status-form');
        const statusInput = document.getElementById('status-input');
        
        form.action = `{{ route('admin.appointments.index') }}/${appointmentId}/update-status`;
        statusInput.value = status;
        form.submit();
    }
}

function confirmDelete(appointmentId, patientName, scheduledAt) {
    if (confirm(`Tem certeza que deseja excluir o agendamento de ${patientName} para ${scheduledAt}?`)) {
        const form = document.getElementById('delete-form');
        form.action = `{{ route('admin.appointments.index') }}/${appointmentId}`;
        form.submit();
    }
}
</script>

</body>
</html>