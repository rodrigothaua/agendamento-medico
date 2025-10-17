<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Relatórios - Clínica Saúde</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/settings-api.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .sidebar-link {
            transition: all 0.3s ease;
        }
        .sidebar-link:hover {
            background-color: rgba(99, 102, 241, 0.1);
            border-left: 4px solid #6366f1;
        }
        .sidebar-link.active {
            background-color: rgba(99, 102, 241, 0.15);
            border-left: 4px solid #6366f1;
            color: #6366f1;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                z-index: 50;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-link {
                padding: 1rem;
                margin: 0.25rem 0.5rem;
                border-radius: 0.5rem;
                border-left: none;
            }
            .sidebar-link:hover,
            .sidebar-link.active {
                background-color: rgba(99, 102, 241, 0.15);
                border-left: none;
                border-radius: 0.5rem;
            }
        }
        .mobile-overlay {
            backdrop-filter: blur(2px);
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
</head>
<body>

<div class="flex min-h-screen bg-gray-50">
    <!-- Sidebar Component -->
    <x-admin-sidebar :activeRoute="'admin.reports.index'" />

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden md:ml-0">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <button id="mobile-menu-button" class="md:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 mr-3">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div>
                            <h1 class="text-xl md:text-2xl font-bold text-gray-900">Relatórios</h1>
                            <p class="text-xs md:text-sm text-gray-600 hidden sm:block">Análise completa dos dados da clínica</p>
                        </div>
                    </div>

                    <!-- Period Filter -->
                    <form method="GET" class="flex items-center space-x-2">
                        <select name="period" onchange="this.form.submit()" 
                                class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Esta Semana</option>
                            <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Este Mês</option>
                            <option value="quarter" {{ $period === 'quarter' ? 'selected' : '' }}>Este Trimestre</option>
                            <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Este Ano</option>
                        </select>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
            <div class="max-w-7xl mx-auto py-4 md:py-6 px-4 sm:px-6 lg:px-8">

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white card p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Agendamentos</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_appointments'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white card p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Novos Pacientes</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_patients'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white card p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Receita</p>
                                <p class="text-2xl font-semibold text-gray-900">R$ {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white card p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Média Diária</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['avg_daily_appointments'], 1) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Appointments by Status -->
                    <div class="bg-white card p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Agendamentos por Status</h3>
                        <div class="chart-container">
                            <canvas id="appointmentsStatusChart"></canvas>
                        </div>
                    </div>

                    <!-- Revenue by Method -->
                    <div class="bg-white card p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Receita por Método</h3>
                        <div class="chart-container">
                            <canvas id="revenueMethodChart"></canvas>
                        </div>
                    </div>

                    <!-- Daily Trends -->
                    <div class="bg-white card p-6 lg:col-span-2">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Tendências Diárias</h3>
                        <div class="chart-container" style="height: 400px;">
                            <canvas id="dailyTrendsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <a href="{{ route('admin.reports.appointments') }}" class="bg-white card p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-gray-900">Relatório de Agendamentos</h4>
                                <p class="text-sm text-gray-600">Análise detalhada dos agendamentos</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.reports.patients') }}" class="bg-white card p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-gray-900">Relatório de Pacientes</h4>
                                <p class="text-sm text-gray-600">Crescimento e análise de pacientes</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.reports.financial') }}" class="bg-white card p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-gray-900">Relatório Financeiro</h4>
                                <p class="text-sm text-gray-600">Análise de receitas e pagamentos</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu functionality
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const closeSidebarButton = document.getElementById('close-sidebar');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobile-menu-overlay');

    function toggleMobileMenu() {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('hidden');
        document.body.classList.toggle('overflow-hidden');
    }

    function closeMobileMenu() {
        sidebar.classList.remove('open');
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', toggleMobileMenu);
    }

    if (closeSidebarButton) {
        closeSidebarButton.addEventListener('click', closeMobileMenu);
    }

    if (overlay) {
        overlay.addEventListener('click', closeMobileMenu);
    }

    // Charts initialization
    initializeCharts();
});

function initializeCharts() {
    // Appointments by Status Chart
    const statusData = @json($chartsData['appointments_by_status']);
    const statusCtx = document.getElementById('appointmentsStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Confirmados', 'Pendentes', 'Cancelados'],
            datasets: [{
                data: [
                    statusData.confirmed || 0,
                    statusData.pending || 0,
                    statusData.cancelled || 0
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

    // Revenue by Method Chart
    const methodData = @json($chartsData['revenue_by_method']);
    const methodCtx = document.getElementById('revenueMethodChart').getContext('2d');
    new Chart(methodCtx, {
        type: 'pie',
        data: {
            labels: ['PIX', 'Cartão de Crédito'],
            datasets: [{
                data: [
                    methodData.pix || 0,
                    methodData.credit_card || 0
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
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Daily Trends Chart
    const trendsData = @json($chartsData['daily_trends']);
    const trendsCtx = document.getElementById('dailyTrendsChart').getContext('2d');
    
    const dates = Object.keys(trendsData.appointments).concat(Object.keys(trendsData.revenue));
    const uniqueDates = [...new Set(dates)].sort();
    
    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: uniqueDates,
            datasets: [{
                label: 'Agendamentos',
                data: uniqueDates.map(date => trendsData.appointments[date] || 0),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                yAxisID: 'y'
            }, {
                label: 'Receita (R$)',
                data: uniqueDates.map(date => trendsData.revenue[date] || 0),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Data'
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Agendamentos'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Receita (R$)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
}
</script>

<script>
// Inicializar o nome da clínica quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    updateClinicName();
});
</script>

</body>
</html>