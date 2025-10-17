<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Clínica Saúde</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <!-- Mobile menu overlay -->
    <div id="mobile-menu-overlay" class="mobile-overlay fixed inset-0 z-40 bg-black bg-opacity-50 hidden md:hidden"></div>
    
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar fixed md:relative inset-y-0 left-0 z-50 w-64 bg-white shadow-lg md:translate-x-0">
        <div class="flex items-center justify-between h-16 md:h-20 border-b border-gray-200 px-4">
            <div class="flex items-center space-x-2">
                <div class="w-6 h-6 md:w-8 md:h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-3 h-3 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h2 class="text-lg md:text-xl font-bold text-gray-800">Clínica</h2>
            </div>
            <button id="close-sidebar" class="md:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <nav class="mt-4 md:mt-6 flex-1 px-2 md:px-4 space-y-1 md:space-y-2">
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-gray-700 rounded-lg font-medium">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 md:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2V7"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('admin.appointments.index') }}" class="sidebar-link flex items-center px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-gray-700 rounded-lg font-medium">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 md:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Agendamentos</span>
                </a>
                
                <a href="{{ route('admin.patients.index') }}" class="sidebar-link flex items-center px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-gray-700 rounded-lg font-medium">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 md:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                    </svg>
                    <span>Pacientes</span>
                </a>
                
                <a href="{{ route('admin.payments.index') }}" class="sidebar-link flex items-center px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-gray-700 rounded-lg font-medium">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 md:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Pagamentos</span>
                </a>
                
                <a href="{{ route('admin.reports.index') }}" class="sidebar-link active flex items-center px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-gray-700 rounded-lg font-medium">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 md:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Relatórios</span>
                </a>
                
                <a href="#" class="sidebar-link flex items-center px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-gray-700 rounded-lg font-medium">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 md:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Configurações</span>
                </a>
            </div>
            
            <!-- User Section -->
            <div class="mt-6 md:mt-8 pt-4 md:pt-6 border-t border-gray-200">
                <div class="px-2 md:px-4">
                    <div class="flex items-center mb-3 md:mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-indigo-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-medium text-sm md:text-base">
                                    {{ substr(session('user_name') ?? 'M', 0, 1) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm md:text-base font-medium text-gray-700 truncate max-w-32">{{ session('user_name') ?? 'Médico' }}</p>
                            <p class="text-xs md:text-sm text-gray-500">Administrador</p>
                        </div>
                    </div>
                    
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center justify-center px-3 md:px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                            <svg class="h-3 h-3 md:h-4 md:w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-xs md:text-sm">Sair</span>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </div>

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

</body>
</html>