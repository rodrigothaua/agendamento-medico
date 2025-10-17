<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Pagamentos - Clínica Saúde</title>
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
        }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .card { box-shadow: none !important; border: 1px solid #e5e7eb; }
        }
    </style>
</head>
<body>

<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6 no-print">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Relatório de Pagamentos</h1>
                    <p class="text-sm text-gray-600">
                        Período: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} até {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                    </p>
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
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Imprimir
                    </button>
                </div>
            </div>
        </div>

        <!-- Date Filter -->
        <div class="bg-white rounded-lg shadow mb-6 no-print">
            <div class="p-6">
                <form method="GET" action="{{ route('admin.payments.report') }}" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Data Inicial</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" 
                               class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Data Final</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" 
                               class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Gerar Relatório
                    </button>
                </form>
            </div>
        </div>

        <!-- Print Header (only visible when printing) -->
        <div class="hidden print:block mb-6">
            <div class="text-center border-b pb-4">
                <h1 class="text-2xl font-bold">Clínica Saúde</h1>
                <h2 class="text-lg">Relatório de Pagamentos</h2>
                <p class="text-sm text-gray-600">
                    Período: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} até {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                </p>
                <p class="text-xs text-gray-500">Gerado em: {{ now()->format('d/m/Y \à\s H:i') }}</p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total Payments -->
            <div class="bg-white card p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total de Pagamentos</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $summary['total_payments'] }}</p>
                        <p class="text-sm text-gray-500">R$ {{ number_format($summary['total_amount'], 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Approved Payments -->
            <div class="bg-white card p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pagamentos Aprovados</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $summary['approved_count'] }}</p>
                        <p class="text-sm text-gray-500">R$ {{ number_format($summary['approved_amount'], 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Pending Payments -->
            <div class="bg-white card p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pagamentos Pendentes</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $summary['pending_count'] }}</p>
                        <p class="text-sm text-gray-500">R$ {{ number_format($summary['pending_amount'], 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Failed Payments -->
            <div class="bg-white card p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pagamentos Rejeitados</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $summary['failed_count'] }}</p>
                        <p class="text-sm text-gray-500">R$ {{ number_format($summary['failed_amount'], 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods Summary -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white card p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pagamentos por Método</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-600">PIX</span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium">{{ $summary['pix_count'] }} pagamentos</div>
                            <div class="text-xs text-gray-500">R$ {{ number_format($summary['pix_amount'], 2, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-600">Cartão de Crédito</span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium">{{ $summary['credit_count'] }} pagamentos</div>
                            <div class="text-xs text-gray-500">R$ {{ number_format($summary['credit_amount'], 2, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white card p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Taxa de Conversão</h3>
                <div class="space-y-4">
                    @php
                        $approvalRate = $summary['total_payments'] > 0 ? ($summary['approved_count'] / $summary['total_payments']) * 100 : 0;
                        $pendingRate = $summary['total_payments'] > 0 ? ($summary['pending_count'] / $summary['total_payments']) * 100 : 0;
                        $failureRate = $summary['total_payments'] > 0 ? ($summary['failed_count'] / $summary['total_payments']) * 100 : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm">
                            <span>Taxa de Aprovação</span>
                            <span class="font-medium">{{ number_format($approvalRate, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $approvalRate }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm">
                            <span>Taxa de Pendência</span>
                            <span class="font-medium">{{ number_format($pendingRate, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ $pendingRate }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm">
                            <span>Taxa de Rejeição</span>
                            <span class="font-medium">{{ number_format($failureRate, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                            <div class="bg-red-600 h-2 rounded-full" style="width: {{ $failureRate }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Lista de Pagamentos</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Transação</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($payments as $payment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $payment->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $payment->appointment->patient->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $payment->appointment->patient->cpf }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    R$ {{ number_format($payment->amount, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($payment->method === 'pix') bg-blue-100 text-blue-800
                                        @else bg-purple-100 text-purple-800 @endif">
                                        @if($payment->method === 'pix') PIX @else Cartão @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($payment->status === 'approved') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        @switch($payment->status)
                                            @case('pending') Pendente @break
                                            @case('approved') Aprovado @break
                                            @case('failed') Rejeitado @break
                                        @endswitch
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 font-mono">
                                    {{ $payment->transaction_id ? substr($payment->transaction_id, 0, 15) . '...' : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <p>Nenhum pagamento encontrado no período selecionado.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Report Footer -->
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Relatório gerado em {{ now()->format('d/m/Y \à\s H:i') }} - Total de registros: {{ $payments->count() }}</p>
        </div>
    </div>
</div>

<script>
// Print functionality
window.addEventListener('beforeprint', function() {
    document.querySelectorAll('.no-print').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.print\\:block').forEach(el => el.style.display = 'block');
});

window.addEventListener('afterprint', function() {
    document.querySelectorAll('.no-print').forEach(el => el.style.display = '');
    document.querySelectorAll('.print\\:block').forEach(el => el.style.display = 'none');
});
</script>

</body>
</html>