<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamentos - Clínica Saúde</title>
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
                
                <a href="{{ route('admin.payments.index') }}" class="sidebar-link active flex items-center px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-gray-700 rounded-lg font-medium">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 md:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Pagamentos</span>
                    <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        {{ $stats['total'] }}
                    </span>
                </a>
                
                <a href="{{ route('admin.reports.index') }}" class="sidebar-link flex items-center px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-gray-700 rounded-lg font-medium">
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
                            <h1 class="text-xl md:text-2xl font-bold text-gray-900">Pagamentos</h1>
                            <p class="text-xs md:text-sm text-gray-600 hidden sm:block">Gerencie todos os pagamentos da clínica</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.payments.report') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Relatório
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
            <div class="max-w-7xl mx-auto py-4 md:py-6 px-4 sm:px-6 lg:px-8">

                <!-- Alerts -->
                @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                                <p class="text-xs text-gray-500">R$ {{ number_format($stats['total_amount'], 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Aprovados</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['approved'] }}</p>
                                <p class="text-xs text-gray-500">R$ {{ number_format($stats['approved_amount'], 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="flex items-center">
                            <div class="p-2 bg-yellow-100 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Pendentes</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending'] }}</p>
                                <p class="text-xs text-gray-500">R$ {{ number_format($stats['pending_amount'], 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="flex items-center">
                            <div class="p-2 bg-indigo-100 rounded-lg">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Hoje</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['today_payments'] }}</p>
                                <p class="text-xs text-gray-500">R$ {{ number_format($stats['today_amount'], 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-6">
                        <form method="GET" action="{{ route('admin.payments.index') }}" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Paciente ou ID transação"
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="all">Todos</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Aprovado</option>
                                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Rejeitado</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Método</label>
                                    <select name="method" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="all">Todos</option>
                                        <option value="pix" {{ request('method') == 'pix' ? 'selected' : '' }}>PIX</option>
                                        <option value="credit_card" {{ request('method') == 'credit_card' ? 'selected' : '' }}>Cartão de Crédito</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Período</label>
                                    <select name="period" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="">Todos</option>
                                        <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hoje</option>
                                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Esta Semana</option>
                                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Este Mês</option>
                                        <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Mês Passado</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex justify-between items-center">
                                <div class="flex space-x-2">
                                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-200">
                                        Filtrar
                                    </button>
                                    <a href="{{ route('admin.payments.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition duration-200">
                                        Limpar
                                    </a>
                                </div>
                                <div class="text-sm text-gray-600">
                                    Mostrando {{ $payments->count() }} de {{ $payments->total() }} pagamentos
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Payments Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($payments as $payment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-white">
                                                            {{ substr($payment->appointment->patient->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $payment->appointment->patient->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $payment->appointment->patient->cpf }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900">R$ {{ number_format($payment->amount, 2, ',', '.') }}</div>
                                            @if($payment->transaction_id)
                                                <div class="text-xs text-gray-500">ID: {{ substr($payment->transaction_id, 0, 10) }}...</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($payment->method === 'pix') bg-blue-100 text-blue-800
                                                @else bg-purple-100 text-purple-800 @endif">
                                                @if($payment->method === 'pix') PIX @else Cartão @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <form method="POST" action="{{ route('admin.payments.update-status', $payment->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" onchange="this.form.submit()" 
                                                        class="text-sm rounded-md border-0 py-1 px-2 focus:ring-2 focus:ring-indigo-500
                                                        @if($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                                        @elseif($payment->status === 'approved') bg-green-100 text-green-800
                                                        @else bg-red-100 text-red-800 @endif">
                                                    <option value="pending" {{ $payment->status === 'pending' ? 'selected' : '' }}>Pendente</option>
                                                    <option value="approved" {{ $payment->status === 'approved' ? 'selected' : '' }}>Aprovado</option>
                                                    <option value="failed" {{ $payment->status === 'failed' ? 'selected' : '' }}>Rejeitado</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $payment->created_at->format('d/m/Y') }}</div>
                                            <div class="text-sm text-gray-500">{{ $payment->created_at->format('H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <!-- Ver Detalhes -->
                                                <a href="{{ route('admin.payments.show', $payment->id) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900" title="Ver detalhes">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>

                                                <!-- Excluir -->
                                                @if($payment->status !== 'approved')
                                                    <button onclick="confirmDelete({{ $payment->id }}, '{{ $payment->appointment->patient->name }}')" 
                                                            class="text-red-600 hover:text-red-900" title="Excluir">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum pagamento encontrado</h3>
                                            <p class="mt-1 text-sm text-gray-500">Os pagamentos aparecerão aqui conforme forem criados.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($payments->hasPages())
                        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                            {{ $payments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Form para exclusão -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

    // Close menu when clicking on a link (mobile)
    const sidebarLinks = sidebar.querySelectorAll('a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 768) {
                closeMobileMenu();
            }
        });
    });

    // Close menu on resize to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            closeMobileMenu();
        }
    });

    // Handle escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !sidebar.classList.contains('hidden')) {
            closeMobileMenu();
        }
    });
});

function confirmDelete(paymentId, patientName) {
    if (confirm(`Tem certeza que deseja excluir o pagamento do paciente ${patientName}?`)) {
        const form = document.getElementById('delete-form');
        form.action = `{{ route('admin.payments.index') }}/${paymentId}`;
        form.submit();
    }
}
</script>

</body>
</html>