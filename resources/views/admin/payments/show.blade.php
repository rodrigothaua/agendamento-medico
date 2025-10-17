<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento #{{ $payment->id }} - Clínica Saúde</title>
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
                    <div class="h-16 w-16 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Pagamento #{{ $payment->id }}</h1>
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span>{{ $payment->appointment->patient->name }}</span>
                            <span>•</span>
                            <span>Criado em {{ $payment->created_at->format('d/m/Y \à\s H:i') }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 md:mt-0 flex space-x-3">
                    <a href="{{ route('admin.payments.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                    <button onclick="window.print()" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Imprimir
                    </button>
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
                <!-- Payment Information -->
                <div class="bg-white card p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informações do Pagamento</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Valor</label>
                            <p class="mt-1 text-2xl font-bold text-gray-900">R$ {{ number_format($payment->amount, 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($payment->status === 'approved') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    @switch($payment->status)
                                        @case('pending') Pendente @break
                                        @case('approved') Aprovado @break
                                        @case('failed') Rejeitado @break
                                    @endswitch
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Método de Pagamento</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($payment->method === 'pix') bg-blue-100 text-blue-800
                                    @else bg-purple-100 text-purple-800 @endif">
                                    @if($payment->method === 'pix') PIX @else Cartão de Crédito @endif
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Data de Criação</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $payment->created_at->format('d/m/Y \à\s H:i') }}</p>
                        </div>
                        @if($payment->transaction_id)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">ID da Transação</label>
                                <p class="mt-1 text-sm text-gray-900 font-mono">{{ $payment->transaction_id }}</p>
                            </div>
                        @endif
                        @if($payment->paid_at)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Data do Pagamento</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $payment->paid_at->format('d/m/Y \à\s H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Patient Information -->
                <div class="bg-white card p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informações do Paciente</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nome Completo</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $payment->appointment->patient->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">CPF</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $payment->appointment->patient->cpf }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">E-mail</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <a href="mailto:{{ $payment->appointment->patient->email }}" class="text-indigo-600 hover:text-indigo-800">
                                    {{ $payment->appointment->patient->email }}
                                </a>
                            </p>
                        </div>
                        @if($payment->appointment->patient->phone)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Telefone</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    <a href="tel:{{ $payment->appointment->patient->phone }}" class="text-indigo-600 hover:text-indigo-800">
                                        {{ $payment->appointment->patient->phone }}
                                    </a>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Appointment Information -->
                <div class="bg-white card p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informações do Agendamento</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Data e Horário</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $payment->appointment->scheduled_at->format('d/m/Y \à\s H:i') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status do Agendamento</label>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($payment->appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($payment->appointment->status === 'confirmed') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    @switch($payment->appointment->status)
                                        @case('pending') Pendente @break
                                        @case('confirmed') Confirmado @break
                                        @case('cancelled') Cancelado @break
                                    @endswitch
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Criado em</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $payment->appointment->created_at->format('d/m/Y \à\s H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Ação</label>
                            <p class="mt-1">
                                <a href="{{ route('admin.appointments.show', $payment->appointment->id) }}" 
                                   class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                    Ver agendamento completo →
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white card p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ações Rápidas</h3>
                    <div class="space-y-3">
                        <!-- Update Status -->
                        <form method="POST" action="{{ route('admin.payments.update-status', $payment->id) }}">
                            @csrf
                            @method('PATCH')
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alterar Status</label>
                            <div class="flex space-x-2">
                                <select name="status" class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm">
                                    <option value="pending" {{ $payment->status === 'pending' ? 'selected' : '' }}>Pendente</option>
                                    <option value="approved" {{ $payment->status === 'approved' ? 'selected' : '' }}>Aprovado</option>
                                    <option value="failed" {{ $payment->status === 'failed' ? 'selected' : '' }}>Rejeitado</option>
                                </select>
                                <button type="submit" 
                                        class="px-3 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                                    Salvar
                                </button>
                            </div>
                        </form>

                        <hr>

                        <!-- View Patient -->
                        <a href="{{ route('admin.patients.show', $payment->appointment->patient->id) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Ver Paciente
                        </a>

                        <!-- View Appointment -->
                        <a href="{{ route('admin.appointments.show', $payment->appointment->id) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Ver Agendamento
                        </a>

                        @if($payment->status !== 'approved')
                            <button onclick="confirmDelete({{ $payment->id }}, '{{ $payment->appointment->patient->name }}')" 
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Excluir Pagamento
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Payment Timeline -->
                <div class="bg-white card p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Histórico</h3>
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <!-- Created -->
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex items-start space-x-3">
                                        <div class="relative">
                                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm text-gray-500">
                                                <span class="font-medium text-gray-900">Pagamento criado</span>
                                                <span class="whitespace-nowrap">{{ $payment->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="mt-1 text-xs text-gray-500">
                                                Valor: R$ {{ number_format($payment->amount, 2, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            @if($payment->updated_at != $payment->created_at)
                                <!-- Updated -->
                                <li>
                                    <div class="relative pb-8">
                                        @if($payment->paid_at)
                                            <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <div class="h-10 w-10 rounded-full 
                                                    @if($payment->status === 'approved') bg-green-500
                                                    @elseif($payment->status === 'failed') bg-red-500
                                                    @else bg-yellow-500 @endif
                                                    flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        @if($payment->status === 'approved')
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        @elseif($payment->status === 'failed')
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        @else
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        @endif
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm text-gray-500">
                                                    <span class="font-medium text-gray-900">Status alterado para: 
                                                        @switch($payment->status)
                                                            @case('pending') Pendente @break
                                                            @case('approved') Aprovado @break
                                                            @case('failed') Rejeitado @break
                                                        @endswitch
                                                    </span>
                                                    <span class="whitespace-nowrap">{{ $payment->updated_at->format('d/m/Y H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif

                            @if($payment->paid_at)
                                <!-- Paid -->
                                <li>
                                    <div class="relative">
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <div class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm text-gray-500">
                                                    <span class="font-medium text-gray-900">Pagamento processado</span>
                                                    <span class="whitespace-nowrap">{{ $payment->paid_at->format('d/m/Y H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
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
function confirmDelete(paymentId, patientName) {
    if (confirm(`Tem certeza que deseja excluir o pagamento do paciente ${patientName}?\n\nEsta ação não pode ser desfeita.`)) {
        const form = document.getElementById('delete-form');
        form.action = `{{ route('admin.payments.index') }}/${paymentId}`;
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