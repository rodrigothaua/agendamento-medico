<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Financeiro - Clínica Saúde</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .card {
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12pt; }
            .card { box-shadow: none; border: 1px solid #ddd; }
        }
    </style>
</head>
<body class="bg-gray-50">

<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Relatório Financeiro</h1>
            <p class="text-sm text-gray-600 mt-1">Análise de receitas e pagamentos - {{ ucfirst($period) }}</p>
        </div>
        
        <div class="mt-4 sm:mt-0 flex space-x-3 no-print">
            <a href="{{ route('admin.reports.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Voltar
            </a>
            
            <button onclick="window.print()" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Imprimir
            </button>

            <a href="{{ route('admin.reports.export', ['type' => 'financial', 'period' => $period]) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Exportar CSV
            </a>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white card p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Receita Total</p>
                    <p class="text-2xl font-semibold text-gray-900">R$ {{ number_format($statistics['total_revenue'], 2, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white card p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pagos</p>
                    <p class="text-2xl font-semibold text-gray-900">R$ {{ number_format($statistics['paid_amount'], 2, ',', '.') }}</p>
                    <p class="text-xs text-blue-600">{{ number_format($statistics['paid_percentage'], 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white card p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pendentes</p>
                    <p class="text-2xl font-semibold text-gray-900">R$ {{ number_format($statistics['pending_amount'], 2, ',', '.') }}</p>
                    <p class="text-xs text-yellow-600">{{ number_format($statistics['pending_percentage'], 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white card p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Média Diária</p>
                    <p class="text-2xl font-semibold text-gray-900">R$ {{ number_format($statistics['avg_daily_revenue'], 2, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods Stats -->
    <div class="bg-white card p-6 mb-8">
        <h3 class="text-lg font-medium text-gray-900 mb-6">Métodos de Pagamento</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-full">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">PIX</p>
                        <p class="text-xs text-gray-500">{{ $paymentMethods['pix']['count'] ?? 0 }} pagamentos</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-lg font-semibold text-gray-900">R$ {{ number_format($paymentMethods['pix']['amount'] ?? 0, 2, ',', '.') }}</p>
                    <p class="text-xs text-blue-600">{{ number_format($paymentMethods['pix']['percentage'] ?? 0, 1) }}%</p>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-full">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Cartão de Crédito</p>
                        <p class="text-xs text-gray-500">{{ $paymentMethods['credit_card']['count'] ?? 0 }} pagamentos</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-lg font-semibold text-gray-900">R$ {{ number_format($paymentMethods['credit_card']['amount'] ?? 0, 2, ',', '.') }}</p>
                    <p class="text-xs text-purple-600">{{ number_format($paymentMethods['credit_card']['percentage'] ?? 0, 1) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue Trend Chart -->
        <div class="bg-white card p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tendência de Receita</h3>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Payment Status Chart -->
        <div class="bg-white card p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Status dos Pagamentos</h3>
            <div class="chart-container">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Payment Methods Chart -->
        <div class="bg-white card p-6 lg:col-span-2">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Receita por Método de Pagamento</h3>
            <div class="chart-container">
                <canvas id="methodsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Earning Days -->
    <div class="bg-white card p-6 mb-8">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Dias com Maior Receita</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($topDays as $day)
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-2xl font-semibold text-gray-900">R$ {{ number_format($day->total, 2, ',', '.') }}</p>
                <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}</p>
                <p class="text-xs text-gray-500">{{ $day->payments_count }} pagamentos</p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="bg-white card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Pagamentos Recentes</h3>
            <a href="{{ route('admin.payments.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm no-print">
                Ver todos →
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentPayments as $payment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $payment->appointment->patient->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            R$ {{ number_format($payment->amount, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @switch($payment->method)
                                @case('pix')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        PIX
                                    </span>
                                    @break
                                @case('credit_card')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Cartão de Crédito
                                    </span>
                                    @break
                                @default
                                    {{ ucfirst($payment->method) }}
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($payment->status)
                                @case('approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aprovado
                                    </span>
                                    @break
                                @case('pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pendente
                                    </span>
                                    @break
                                @case('failed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Falhou
                                    </span>
                                    @break
                            @endswitch
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});

function initializeCharts() {
    // Revenue Trend Chart
    const revenueData = @json($chartData['revenue_trend']);
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    
    const dates = Object.keys(revenueData).sort();
    const values = dates.map(date => revenueData[date]);
    
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Receita (R$)',
                data: values,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Payment Status Chart
    const statusData = @json($chartData['payment_status']);
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Aprovados', 'Pendentes', 'Falharam'],
            datasets: [{
                data: [
                    statusData.approved || 0,
                    statusData.pending || 0,
                    statusData.failed || 0
                ],
                backgroundColor: [
                    '#10b981',
                    '#f59e0b',
                    '#ef4444'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Payment Methods Chart
    const methodsData = @json($chartData['payment_methods']);
    const methodsCtx = document.getElementById('methodsChart').getContext('2d');
    
    new Chart(methodsCtx, {
        type: 'bar',
        data: {
            labels: ['PIX', 'Cartão de Crédito'],
            datasets: [{
                label: 'Receita (R$)',
                data: [
                    methodsData.pix || 0,
                    methodsData.credit_card || 0
                ],
                backgroundColor: [
                    '#3b82f6',
                    '#8b5cf6'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}
</script>

</body>
</html>