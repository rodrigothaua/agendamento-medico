<!-- Form para exclusão -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Agendamentos - Clínica Saúde</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/settings-api.js') }}"></script>
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
        
        /* Calendar Styles */
        .view-toggle {
            background-color: #f3f4f6;
            color: #6b7280;
        }
        
        .view-toggle.active {
            background-color: #6366f1;
            color: #ffffff;
        }
        
        .calendar-day {
            min-height: 120px;
            border-right: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            position: relative;
        }
        
        .calendar-day:nth-child(7n) {
            border-right: none;
        }
        
        .calendar-day.other-month {
            background-color: #f9fafb;
            color: #9ca3af;
        }
        
        .calendar-day.today {
            background-color: #eff6ff;
            border: 2px solid #3b82f6;
        }
        
        .calendar-day .day-number {
            position: absolute;
            top: 8px;
            left: 8px;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .calendar-day.today .day-number {
            color: #3b82f6;
        }
        
        .appointment-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin: 1px;
            display: inline-block;
        }
        
        .appointment-dot.confirmed {
            background-color: #10b981;
        }
        
        .appointment-dot.pending {
            background-color: #f59e0b;
        }
        
        .appointment-dot.canceled {
            background-color: #ef4444;
        }
        
        .appointment-item {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 4px;
            padding: 2px 4px;
            margin: 2px;
            font-size: 0.75rem;
            line-height: 1.2;
            border-left: 3px solid;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .appointment-item:hover {
            background-color: rgba(255, 255, 255, 1);
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .appointment-item.confirmed {
            border-left-color: #10b981;
            background-color: rgba(16, 185, 129, 0.1);
        }
        
        .appointment-item.pending {
            border-left-color: #f59e0b;
            background-color: rgba(245, 158, 11, 0.1);
        }
        
        .appointment-item.canceled {
            border-left-color: #ef4444;
            background-color: rgba(239, 68, 68, 0.1);
        }
        
        .appointments-container {
            position: absolute;
            top: 30px;
            left: 8px;
            right: 8px;
            bottom: 8px;
            overflow-y: auto;
        }
        
        .appointments-container::-webkit-scrollbar {
            width: 4px;
        }
        
        .appointments-container::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .appointments-container::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 2px;
        }
        
        .appointments-container::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .calendar-day {
                min-height: 80px;
            }
            
            .appointment-item {
                font-size: 0.65rem;
                padding: 1px 2px;
                margin: 1px;
            }
            
            .day-number {
                font-size: 0.75rem;
            }
            
            .appointments-container {
                top: 25px;
            }
        }
        
        @media (max-width: 640px) {
            .calendar-day {
                min-height: 60px;
            }
            
            .appointment-item {
                font-size: 0.6rem;
                line-height: 1;
            }
            
            .day-number {
                font-size: 0.7rem;
            }
        }
        
        /* Modal Styles */
        #appointment-modal {
            backdrop-filter: blur(2px);
            animation: fadeIn 0.3s ease-in-out;
        }
        
        #appointment-modal .bg-white {
            animation: slideInUp 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideInUp {
            from { 
                opacity: 0; 
                transform: translateY(20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }
        
        /* Modal responsive */
        @media (max-width: 640px) {
            #appointment-modal .bg-white {
                margin: 1rem;
                width: calc(100% - 2rem);
            }
            
            #appointment-modal .grid-cols-3 {
                grid-template-columns: 1fr;
            }
            
            #appointment-modal .md\\:grid-cols-2 {
                grid-template-columns: 1fr;
            }
            
            #appointment-modal .md\\:grid-cols-3 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="flex min-h-screen bg-gray-50">
    <!-- Sidebar Component -->
    <x-admin-sidebar :activeRoute="'admin.appointments.index'" :stats="$stats" />

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
                            <h1 class="text-xl md:text-2xl font-bold text-gray-900">Agendamentos</h1>
                            <p class="text-xs md:text-sm text-gray-600 hidden sm:block">Gerencie todos os agendamentos da clínica</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
            <div class="max-w-7xl mx-auto py-4 md:py-6 px-4 sm:px-6 lg:px-8">

                <!-- Alerts -->
                <!-- Feedback visual removido, apenas toast será exibido -->

                @include('components.toast')

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
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
                                <p class="text-sm font-medium text-gray-600">Confirmados</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['confirmed'] }}</p>
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
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="flex items-center">
                            <div class="p-2 bg-red-100 rounded-lg">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Cancelados</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['canceled'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Toggle and Filters -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-6">
                        <!-- View Toggle -->
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex space-x-2">
                                <button id="list-view-btn" class="view-toggle active flex items-center px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                    </svg>
                                    Lista
                                </button>
                                <button id="calendar-view-btn" class="view-toggle flex items-center px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Calendário
                                </button>
                            </div>
                        </div>

                        <!-- Filters -->
                        <form method="GET" action="{{ route('admin.appointments.index') }}" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Todos</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendentes</option>
                                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmados</option>
                                        <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Cancelados</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Período</label>
                                    <select name="period" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="">Todos</option>
                                        <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hoje</option>
                                        <option value="tomorrow" {{ request('period') == 'tomorrow' ? 'selected' : '' }}>Amanhã</option>
                                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Esta Semana</option>
                                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Este Mês</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Específica</label>
                                    <input type="date" name="date" value="{{ request('date') }}" 
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Paciente</label>
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, CPF ou Email"
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>

                            <div class="flex justify-between items-center">
                                <div class="flex space-x-2">
                                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-200">
                                        Filtrar
                                    </button>
                                    <a href="{{ route('admin.appointments.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition duration-200">
                                        Limpar
                                    </a>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <span id="list-counter">Mostrando {{ $appointments->count() }} de {{ $appointments->total() }} agendamentos</span>
                                    <span id="calendar-counter" style="display: none;">Visualização em calendário</span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Calendar View -->
                <div id="calendar-view" class="bg-white rounded-lg shadow overflow-hidden" style="display: none;">
                    <div class="p-6">
                        <!-- Calendar Header -->
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center space-x-4">
                                <button id="prev-month" class="p-2 rounded-md bg-gray-100 hover:bg-gray-200 transition-colors">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>
                                <h2 id="calendar-month-year" class="text-xl font-semibold text-gray-900"></h2>
                                <button id="next-month" class="p-2 rounded-md bg-gray-100 hover:bg-gray-200 transition-colors">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                            <button id="today-btn" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors text-sm">
                                Hoje
                            </button>
                        </div>

                        <!-- Calendar Grid -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <!-- Calendar Days Header -->
                            <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
                                <div class="px-3 py-3 text-center text-sm font-medium text-gray-500 border-r border-gray-200">Dom</div>
                                <div class="px-3 py-3 text-center text-sm font-medium text-gray-500 border-r border-gray-200">Seg</div>
                                <div class="px-3 py-3 text-center text-sm font-medium text-gray-500 border-r border-gray-200">Ter</div>
                                <div class="px-3 py-3 text-center text-sm font-medium text-gray-500 border-r border-gray-200">Qua</div>
                                <div class="px-3 py-3 text-center text-sm font-medium text-gray-500 border-r border-gray-200">Qui</div>
                                <div class="px-3 py-3 text-center text-sm font-medium text-gray-500 border-r border-gray-200">Sex</div>
                                <div class="px-3 py-3 text-center text-sm font-medium text-gray-500">Sáb</div>
                            </div>
                            
                            <!-- Calendar Days Grid -->
                            <div id="calendar-grid" class="grid grid-cols-7">
                                <!-- Calendar days will be generated by JavaScript -->
                            </div>
                        </div>

                        <!-- Calendar Legend -->
                        <div class="mt-4 flex flex-wrap gap-4 justify-center text-sm">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded mr-2"></div>
                                <span class="text-gray-600">Confirmado</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-500 rounded mr-2"></div>
                                <span class="text-gray-600">Pendente</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-red-500 rounded mr-2"></div>
                                <span class="text-gray-600">Cancelado</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded mr-2"></div>
                                <span class="text-gray-600">Hoje</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointments Table -->
                <div id="list-view" class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data/Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pagamento</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($appointments as $appointment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-white">
                                                            {{ substr($appointment->patient->name ?? 'N', 0, 1) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $appointment->patient->name ?? 'N/A' }}</div>
                                                    <div class="text-sm text-gray-500">{{ $appointment->patient->cpf ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d/m/Y') }}</div>
                                            <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
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
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($appointment->payment)
                                                <div class="text-sm text-gray-900">R$ {{ number_format($appointment->payment->amount, 2, ',', '.') }}</div>
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
                                            @else
                                                <span class="text-sm text-gray-500">Sem pagamento</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <!-- Alterar Status -->
                                                @if($appointment->status !== 'canceled')
                                                    <div class="relative">
                                                        <select onchange="updateStatus({{ $appointment->id }}, this.value)" 
                                                                class="text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                            <option value="">Alterar Status</option>
                                                            @if($appointment->status === 'pending')
                                                                <option value="confirmed">Confirmar</option>
                                                            @endif
                                                            @if($appointment->status !== 'canceled')
                                                                <option value="canceled">Cancelar</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                @endif

                                                <!-- Ver Detalhes -->
                                                <a href="{{ route('admin.appointments.show', $appointment->id) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900" title="Ver detalhes">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>

                                                <!-- Excluir -->
                                                @if($appointment->status !== 'confirmed' || ($appointment->payment && $appointment->payment->status !== 'approved'))
                            <button onclick="window.confirmDialog.show('Tem certeza que deseja excluir o agendamento de {{ addslashes($appointment->patient->name ?? 'Paciente') }} para {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d/m/Y H:i') }}?', function() { var form = document.getElementById('delete-form'); form.action = '{{ route('admin.appointments.index') }}/{{ $appointment->id }}'; form.submit(); }, 'Confirmação', 'delete')" 
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
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum agendamento encontrado</h3>
                                            <p class="mt-1 text-sm text-gray-500">Tente ajustar os filtros ou aguarde novos agendamentos.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($appointments->hasPages())
                        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                            {{ $appointments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal para detalhes do agendamento -->
<div id="appointment-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Detalhes do Agendamento</h3>
            <button id="close-modal" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">
            <!-- Patient Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Informações do Paciente
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nome</label>
                        <p id="modal-patient-name" class="text-gray-900 font-medium">-</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">CPF</label>
                        <p id="modal-patient-cpf" class="text-gray-900">-</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p id="modal-patient-email" class="text-gray-900">-</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Telefone</label>
                        <p id="modal-patient-phone" class="text-gray-900">-</p>
                    </div>
                </div>
            </div>

            <!-- Appointment Info -->
            <div class="bg-blue-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Dados do Agendamento
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Data</label>
                        <p id="modal-appointment-date" class="text-gray-900 font-medium">-</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Horário</label>
                        <p id="modal-appointment-time" class="text-gray-900 font-medium">-</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <span id="modal-appointment-status" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">-</span>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="text-sm font-medium text-gray-500">Observações</label>
                    <p id="modal-appointment-notes" class="text-gray-900 mt-1">-</p>
                </div>
            </div>

            <!-- Payment Info -->
            <div id="modal-payment-section" class="bg-green-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Informações de Pagamento
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Valor</label>
                        <p id="modal-payment-amount" class="text-gray-900 font-medium">-</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Método</label>
                        <p id="modal-payment-method" class="text-gray-900">-</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <span id="modal-payment-status" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">-</span>
                    </div>
                </div>
            </div>

            <!-- No Payment Info -->
            <div id="modal-no-payment" class="bg-gray-50 rounded-lg p-4 text-center" style="display: none;">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-600">Nenhum pagamento associado a este agendamento</p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-between items-center p-6 border-t border-gray-200 bg-gray-50 rounded-b-lg">
            <div class="flex space-x-2">
                <!-- Status Actions -->
                <div id="modal-status-actions" class="flex space-x-2">
                    <!-- Buttons will be populated by JavaScript -->
                </div>
            </div>
            <div class="flex space-x-2">
                <a id="modal-view-details" href="#" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Ver Página Completa
                </a>
                <button id="close-modal-btn" class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 transition-colors">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Reusable Confirm Dialog Component -->
@include('components.confirm-dialog')

<script>
// Global variables for calendar
let currentDate = new Date();
let appointmentsData = @json($calendarAppointments);

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

    // View toggle functionality
    const listViewBtn = document.getElementById('list-view-btn');
    const calendarViewBtn = document.getElementById('calendar-view-btn');
    const listView = document.getElementById('list-view');
    const calendarView = document.getElementById('calendar-view');
    const listCounter = document.getElementById('list-counter');
    const calendarCounter = document.getElementById('calendar-counter');

    // Toggle between views
    function showListView() {
        listView.style.display = 'block';
        calendarView.style.display = 'none';
        listViewBtn.classList.add('active');
        calendarViewBtn.classList.remove('active');
        listCounter.style.display = 'inline';
        calendarCounter.style.display = 'none';
    }

    function showCalendarView() {
        listView.style.display = 'none';
        calendarView.style.display = 'block';
        listViewBtn.classList.remove('active');
        calendarViewBtn.classList.add('active');
        listCounter.style.display = 'none';
        calendarCounter.style.display = 'inline';
        renderCalendar();
    }

    listViewBtn.addEventListener('click', showListView);
    calendarViewBtn.addEventListener('click', showCalendarView);

    // Calendar functionality
    const prevMonthBtn = document.getElementById('prev-month');
    const nextMonthBtn = document.getElementById('next-month');
    const todayBtn = document.getElementById('today-btn');
    const monthYearDisplay = document.getElementById('calendar-month-year');
    const calendarGrid = document.getElementById('calendar-grid');

    prevMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    nextMonthBtn.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    todayBtn.addEventListener('click', () => {
        currentDate = new Date();
        renderCalendar();
    });

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        // Update month/year display
        const monthNames = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                           'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        monthYearDisplay.textContent = `${monthNames[month]} ${year}`;

        // Clear previous calendar
        calendarGrid.innerHTML = '';

        // Get first day of the month and number of days
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());

        // Generate 42 days (6 weeks)
        for (let i = 0; i < 42; i++) {
            const date = new Date(startDate);
            date.setDate(startDate.getDate() + i);
            
            const dayElement = createCalendarDay(date, month);
            calendarGrid.appendChild(dayElement);
        }
    }

    function createCalendarDay(date, currentMonth) {
        const dayDiv = document.createElement('div');
        dayDiv.className = 'calendar-day relative';
        
        const isOtherMonth = date.getMonth() !== currentMonth;
        const isToday = isDateToday(date);
        
        if (isOtherMonth) {
            dayDiv.classList.add('other-month');
        }
        
        if (isToday) {
            dayDiv.classList.add('today');
        }

        // Day number
        const dayNumber = document.createElement('div');
        dayNumber.className = 'day-number';
        dayNumber.textContent = date.getDate();
        dayDiv.appendChild(dayNumber);

        // Appointments container
        const appointmentsContainer = document.createElement('div');
        appointmentsContainer.className = 'appointments-container';
        
        // Get appointments for this date
        const dayAppointments = getAppointmentsForDate(date);
        
        dayAppointments.forEach(appointment => {
            const appointmentElement = createAppointmentElement(appointment);
            appointmentsContainer.appendChild(appointmentElement);
        });

        dayDiv.appendChild(appointmentsContainer);
        
        return dayDiv;
    }

    function createAppointmentElement(appointment) {
        const appointmentDiv = document.createElement('div');
        appointmentDiv.className = `appointment-item ${appointment.status}`;
        
        const scheduledAt = new Date(appointment.scheduled_at);
        const time = scheduledAt.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        const patientName = appointment.patient ? appointment.patient.name : 'N/A';
        
        appointmentDiv.innerHTML = `
            <div class="font-medium text-xs">${time}</div>
            <div class="text-xs truncate">${patientName}</div>
        `;
        
        appointmentDiv.addEventListener('click', () => {
            appointmentDiv.style.transform = 'scale(0.95)';
            setTimeout(() => {
                appointmentDiv.style.transform = '';
                showAppointmentDetails(appointment);
            }, 100);
        });
        
        return appointmentDiv;
    }

    function getAppointmentsForDate(date) {
        const dateString = date.toISOString().split('T')[0];
        return appointmentsData.filter(appointment => {
            const appointmentDate = new Date(appointment.scheduled_at).toISOString().split('T')[0];
            return appointmentDate === dateString;
        });
    }

    function isDateToday(date) {
        const today = new Date();
        return date.toDateString() === today.toDateString();
    }

    function showAppointmentDetails(appointment) {
        const modal = document.getElementById('appointment-modal');
        
        // Populate patient information
        const patient = appointment.patient || {};
        document.getElementById('modal-patient-name').textContent = patient.name || 'Nome não informado';
        document.getElementById('modal-patient-cpf').textContent = patient.cpf || 'CPF não informado';
        document.getElementById('modal-patient-email').textContent = patient.email || 'Email não informado';
        document.getElementById('modal-patient-phone').textContent = patient.phone || 'Telefone não informado';
        
        // Populate appointment information
        const scheduledAt = new Date(appointment.scheduled_at);
        document.getElementById('modal-appointment-date').textContent = scheduledAt.toLocaleDateString('pt-BR');
        document.getElementById('modal-appointment-time').textContent = scheduledAt.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        document.getElementById('modal-appointment-notes').textContent = appointment.notes || 'Nenhuma observação';
        
        // Status configuration
        const statusConfig = {
            'confirmed': { class: 'bg-green-100 text-green-800', text: 'Confirmado' },
            'pending': { class: 'bg-yellow-100 text-yellow-800', text: 'Pendente' },
            'canceled': { class: 'bg-red-100 text-red-800', text: 'Cancelado' }
        };
        
        const status = statusConfig[appointment.status] || { class: 'bg-gray-100 text-gray-800', text: appointment.status };
        const statusElement = document.getElementById('modal-appointment-status');
        statusElement.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${status.class}`;
        statusElement.textContent = status.text;
        
        // Payment information
        const paymentSection = document.getElementById('modal-payment-section');
        const noPaymentSection = document.getElementById('modal-no-payment');
        
        if (appointment.payment) {
            paymentSection.style.display = 'block';
            noPaymentSection.style.display = 'none';
            
            document.getElementById('modal-payment-amount').textContent = `R$ ${parseFloat(appointment.payment.amount).toLocaleString('pt-BR', { minimumFractionDigits: 2 })}`;
            document.getElementById('modal-payment-method').textContent = appointment.payment.payment_method || 'N/A';
            
            const paymentStatusConfig = {
                'approved': { class: 'bg-green-100 text-green-800', 'text' => 'Pago' },
                'pending': { class: 'bg-yellow-100 text-yellow-800', 'text' => 'Pendente' },
                'failed': { class: 'bg-red-100 text-red-800', 'text' => 'Falhou' },
                'refunded': { class: 'bg-gray-100 text-gray-800', 'text' => 'Estornado' }
            };
            
            const paymentStatus = paymentStatusConfig[appointment.payment.status] || { class: 'bg-gray-100 text-gray-800', text: appointment.payment.status };
            const paymentStatusElement = document.getElementById('modal-payment-status');
            paymentStatusElement.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${paymentStatus.class}`;
            paymentStatusElement.textContent = paymentStatus.text;
        } else {
            paymentSection.style.display = 'none';
            noPaymentSection.style.display = 'block';
        }
        
        // Setup action buttons
        setupModalActions(appointment);
        
        // Set view details link
        document.getElementById('modal-view-details').href = `{{ route('admin.appointments.index') }}/${appointment.id}`;
        
        // Show modal
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    
    function setupModalActions(appointment) {
        const actionsContainer = document.getElementById('modal-status-actions');
        actionsContainer.innerHTML = '';
        
        if (appointment.status !== 'canceled') {
            if (appointment.status === 'pending') {
                const confirmBtn = document.createElement('button');
                confirmBtn.className = 'px-3 py-1.5 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 transition-colors';
                confirmBtn.innerHTML = 'Confirmar';
                confirmBtn.onclick = () => updateStatusFromModal(appointment.id, 'confirmed');
                actionsContainer.appendChild(confirmBtn);
            }
            
            const cancelBtn = document.createElement('button');
            cancelBtn.className = 'px-3 py-1.5 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 transition-colors';
            cancelBtn.innerHTML = 'Cancelar';
            cancelBtn.onclick = () => updateStatusFromModal(appointment.id, 'canceled');
            actionsContainer.appendChild(cancelBtn);
        }
        
        // Delete button (if allowed)
        if (appointment.status !== 'confirmed' || !appointment.payment || appointment.payment.status !== 'approved') {
            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'px-3 py-1.5 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700 transition-colors';
            deleteBtn.innerHTML = 'Excluir';
            deleteBtn.onclick = () => confirmDeleteFromModal(appointment);
            actionsContainer.appendChild(deleteBtn);
        }
    }
    
    function updateStatusFromModal(appointmentId, status) {
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
    
    function confirmDeleteFromModal(appointment) {
        const patientName = appointment.patient ? appointment.patient.name : 'Paciente';
        const scheduledAt = new Date(appointment.scheduled_at);
        const formattedDateTime = scheduledAt.toLocaleDateString('pt-BR') + ' ' + scheduledAt.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        window.confirmDialog.show(`Tem certeza que deseja excluir o agendamento de ${patientName} para ${formattedDateTime}?`, function() {
            const form = document.getElementById('delete-form');
            form.action = `{{ route('admin.appointments.index') }}/${appointment.id}`;
            form.submit();
        }, 'Confirmação', 'delete');
    }
    
    function closeModal() {
        const modal = document.getElementById('appointment-modal');
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Initialize calendar if appointments data is available
    if (appointmentsData && appointmentsData.length > 0) {
        renderCalendar();
    }
    
    // Modal event listeners
    const modal = document.getElementById('appointment-modal');
    const closeModalBtn = document.getElementById('close-modal');
    const closeModalFooterBtn = document.getElementById('close-modal-btn');
    
    // Close modal events
    closeModalBtn.addEventListener('click', closeModal);
    closeModalFooterBtn.addEventListener('click', closeModal);
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
});

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

<script>
// Inicializar o nome da clínica quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    updateClinicName();
});
</script>

</body>
</html>