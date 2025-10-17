<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Configurações - Clínica Saúde</title>
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
        .card:hover {
            transform: translateY(-3px);
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
                
                <a href="{{ route('admin.payments.index') }}" class="sidebar-link flex items-center px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-gray-700 rounded-lg font-medium">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 md:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Pagamentos</span>
                </a>
                
                <a href="{{ route('admin.reports.index') }}" class="sidebar-link flex items-center px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-gray-700 rounded-lg font-medium">
                    <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 md:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Relatórios</span>
                </a>
                
                <a href="{{ route('admin.settings') }}" class="sidebar-link active flex items-center px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-gray-700 rounded-lg font-medium">
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
                        <!-- Mobile menu button -->
                        <button id="mobile-menu-button" class="md:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 mr-3">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div>
                            <h1 class="text-xl md:text-2xl font-bold text-gray-900">Configurações</h1>
                            <p class="text-xs md:text-sm text-gray-600 hidden sm:block">Gerencie as configurações da clínica</p>
                        </div>
                    </div>
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

                <!-- Tabs Navigation -->
                <div class="mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8 overflow-x-auto">
                            <a href="#general" class="tab-link active text-indigo-600 border-indigo-500 py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Informações Gerais
                            </a>
                            <a href="#schedule" class="tab-link text-gray-500 hover:text-gray-700 py-2 px-1 border-b-2 border-transparent hover:border-gray-300 font-medium text-sm whitespace-nowrap">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Horários
                            </a>
                            <a href="#notifications" class="tab-link text-gray-500 hover:text-gray-700 py-2 px-1 border-b-2 border-transparent hover:border-gray-300 font-medium text-sm whitespace-nowrap">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.343 4.343l-1.414 1.414L9 12l6.071-6.071 1.414 1.414L10.414 13.414l4.95 4.95-1.414 1.414L9 14.828 2.929 20.9l-1.414-1.414L7.586 13.414 4.343 10.171z"></path>
                                </svg>
                                Notificações
                            </a>
                            <a href="#blocks" class="tab-link text-gray-500 hover:text-gray-700 py-2 px-1 border-b-2 border-transparent hover:border-gray-300 font-medium text-sm whitespace-nowrap">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18 21l-5.197-5.197m0 0L5.636 5.636M18.364 18.364L12 12"></path>
                                </svg>
                                Bloqueios
                            </a>
                            <a href="#backup" class="tab-link text-gray-500 hover:text-gray-700 py-2 px-1 border-b-2 border-transparent hover:border-gray-300 font-medium text-sm whitespace-nowrap">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Backup
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="space-y-6">
                    
                    <!-- General Settings Tab -->
                    <div id="general-tab" class="tab-content bg-white rounded-lg shadow">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-6">Informações Gerais da Clínica</h2>
                            
                            <form action="{{ route('admin.settings.update.general') }}" method="POST" class="space-y-6">
                                @csrf
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Clinic Name -->
                                    <div>
                                        <label for="clinic_name" class="block text-sm font-medium text-gray-700 mb-2">Nome da Clínica</label>
                                        <input type="text" id="clinic_name" name="clinic_name" 
                                               value="Clínica Saúde"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                               required>
                                    </div>

                                    <!-- Doctor Name -->
                                    <div>
                                        <label for="doctor_name" class="block text-sm font-medium text-gray-700 mb-2">Nome do Médico</label>
                                        <input type="text" id="doctor_name" name="doctor_name" 
                                               value="Dr. João Silva"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                               required>
                                    </div>

                                    <!-- Phone -->
                                    <div>
                                        <label for="clinic_phone" class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                                        <input type="tel" id="clinic_phone" name="clinic_phone" 
                                               value="(11) 99999-9999"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <label for="clinic_email" class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
                                        <input type="email" id="clinic_email" name="clinic_email" 
                                               value="contato@clinicasaude.com"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <!-- Default Price -->
                                    <div>
                                        <label for="default_appointment_price" class="block text-sm font-medium text-gray-700 mb-2">Valor Padrão da Consulta</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-gray-500">R$</span>
                                            <input type="number" step="0.01" id="default_appointment_price" name="default_appointment_price" 
                                                   value="150.00"
                                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                                   required>
                                        </div>
                                    </div>

                                    <!-- Appointment Duration -->
                                    <div>
                                        <label for="appointment_duration" class="block text-sm font-medium text-gray-700 mb-2">Duração da Consulta (minutos)</label>
                                        <select id="appointment_duration" name="appointment_duration" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                                required>
                                            <option value="15">15 minutos</option>
                                            <option value="30" selected>30 minutos</option>
                                            <option value="45">45 minutos</option>
                                            <option value="60">60 minutos</option>
                                            <option value="90">90 minutos</option>
                                            <option value="120">120 minutos</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Address -->
                                <div>
                                    <label for="clinic_address" class="block text-sm font-medium text-gray-700 mb-2">Endereço Completo</label>
                                    <textarea id="clinic_address" name="clinic_address" rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                              placeholder="Rua, número, bairro, cidade, CEP">Rua das Flores, 123, Centro, São Paulo - SP, 01234-567</textarea>
                                </div>

                                <!-- Save Button -->
                                <div class="pt-4">
                                    <button type="submit" 
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                        </svg>
                                        Salvar Alterações
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Schedule Settings Tab -->
                    <div id="schedule-tab" class="tab-content bg-white rounded-lg shadow hidden">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-6">Horários de Funcionamento</h2>
                            
                            <form action="{{ route('admin.settings.update.schedule') }}" method="POST" class="space-y-6">
                                @csrf
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <!-- Work Hours -->
                                    <div>
                                        <label for="work_start_time" class="block text-sm font-medium text-gray-700 mb-2">Horário de Início</label>
                                        <input type="time" id="work_start_time" name="work_start_time" value="08:00"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                    </div>
                                    
                                    <div>
                                        <label for="work_end_time" class="block text-sm font-medium text-gray-700 mb-2">Horário de Fim</label>
                                        <input type="time" id="work_end_time" name="work_end_time" value="18:00"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                    </div>

                                    <div>
                                        <label for="lunch_start_time" class="block text-sm font-medium text-gray-700 mb-2">Início do Almoço</label>
                                        <input type="time" id="lunch_start_time" name="lunch_start_time" value="12:00"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    
                                    <div>
                                        <label for="lunch_end_time" class="block text-sm font-medium text-gray-700 mb-2">Fim do Almoço</label>
                                        <input type="time" id="lunch_end_time" name="lunch_end_time" value="13:00"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <div>
                                        <label for="advance_booking_days" class="block text-sm font-medium text-gray-700 mb-2">Dias de Antecedência para Agendamento</label>
                                        <input type="number" id="advance_booking_days" name="advance_booking_days" value="30" min="1" max="365"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                    </div>
                                    
                                    <div>
                                        <label for="cancellation_hours" class="block text-sm font-medium text-gray-700 mb-2">Horas para Cancelamento</label>
                                        <input type="number" id="cancellation_hours" name="cancellation_hours" value="24" min="1" max="72"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                    </div>
                                </div>

                                <!-- Work Days -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Dias de Funcionamento</label>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        @foreach(['monday' => 'Segunda', 'tuesday' => 'Terça', 'wednesday' => 'Quarta', 'thursday' => 'Quinta', 'friday' => 'Sexta', 'saturday' => 'Sábado', 'sunday' => 'Domingo'] as $day => $label)
                                        <div class="flex items-center">
                                            <input type="checkbox" id="work_day_{{ $day }}" name="work_days[]" value="{{ $day }}" 
                                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                   {{ in_array($day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']) ? 'checked' : '' }}>
                                            <label for="work_day_{{ $day }}" class="ml-2 text-sm text-gray-700">{{ $label }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="pt-4">
                                    <button type="submit" 
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                        </svg>
                                        Salvar Horários
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Notifications Settings Tab -->
                    <div id="notifications-tab" class="tab-content bg-white rounded-lg shadow hidden">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-6">Configurações de Notificações</h2>
                            
                            <form action="{{ route('admin.settings.update.notifications') }}" method="POST" class="space-y-6">
                                @csrf
                                
                                <div class="space-y-6">
                                    <!-- Email Notifications -->
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h3 class="text-md font-medium text-gray-900 mb-4">Notificações por E-mail</h3>
                                        <div class="flex items-center justify-between mb-4">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Ativar Notificações por E-mail</label>
                                                <p class="text-sm text-gray-500">Receber notificações via e-mail</p>
                                            </div>
                                            <input type="checkbox" name="email_notifications" value="1" checked
                                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        </div>
                                    </div>

                                    <!-- SMS Notifications -->
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h3 class="text-md font-medium text-gray-900 mb-4">Notificações por SMS</h3>
                                        <div class="flex items-center justify-between mb-4">
                                            <div>
                                                <label class="text-sm font-medium text-gray-700">Ativar Notificações por SMS</label>
                                                <p class="text-sm text-gray-500">Receber notificações via SMS</p>
                                            </div>
                                            <input type="checkbox" name="sms_notifications" value="1"
                                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        </div>
                                    </div>

                                    <!-- Reminder Settings -->
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h3 class="text-md font-medium text-gray-900 mb-4">Configurações de Lembretes</h3>
                                        <div>
                                            <label for="reminder_hours" class="block text-sm font-medium text-gray-700 mb-2">Horas de Antecedência para Lembrete</label>
                                            <input type="number" id="reminder_hours" name="reminder_hours" value="24" min="1" max="168"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                            <p class="text-sm text-gray-500 mt-1">Entre 1 e 168 horas (7 dias)</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-4">
                                    <button type="submit" 
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                        </svg>
                                        Salvar Notificações
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Schedule Blocks Tab -->
                    <div id="blocks-tab" class="tab-content bg-white rounded-lg shadow hidden">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-lg font-medium text-gray-900">Bloqueios de Agenda</h2>
                                <a href="{{ route('admin.settings.schedule-blocks') }}" 
                                   class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                                    Ver todos os bloqueios →
                                </a>
                            </div>
                            
                            <!-- Add New Block Form -->
                            <div class="bg-gray-50 p-6 rounded-lg mb-6">
                                <h3 class="text-md font-medium text-gray-900 mb-4">Adicionar Novo Bloqueio</h3>
                                
                                <form action="{{ route('admin.settings.schedule-blocks.store') }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Date -->
                                        <div>
                                            <label for="block_date" class="block text-sm font-medium text-gray-700 mb-2">Data</label>
                                            <input type="date" id="block_date" name="date" 
                                                   min="{{ date('Y-m-d') }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                                   required>
                                        </div>

                                        <!-- Block Type -->
                                        <div>
                                            <label for="block_type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Bloqueio</label>
                                            <select id="block_type" name="type" 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                                    required onchange="toggleTimeFields()">
                                                <option value="full_day">Dia Inteiro</option>
                                                <option value="time_range">Horário Específico</option>
                                            </select>
                                        </div>

                                        <!-- Start Time -->
                                        <div id="start_time_field" style="display: none;">
                                            <label for="block_start_time" class="block text-sm font-medium text-gray-700 mb-2">Horário de Início</label>
                                            <input type="time" id="block_start_time" name="start_time" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>

                                        <!-- End Time -->
                                        <div id="end_time_field" style="display: none;">
                                            <label for="block_end_time" class="block text-sm font-medium text-gray-700 mb-2">Horário de Fim</label>
                                            <input type="time" id="block_end_time" name="end_time" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                    </div>

                                    <!-- Reason -->
                                    <div>
                                        <label for="block_reason" class="block text-sm font-medium text-gray-700 mb-2">Motivo (Opcional)</label>
                                        <input type="text" id="block_reason" name="reason" 
                                               placeholder="Ex: Férias, Reunião, Emergência..."
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <!-- Submit Button -->
                                    <div>
                                        <button type="submit" 
                                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Criar Bloqueio
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Existing Blocks List -->
                            <div>
                                <h3 class="text-md font-medium text-gray-900 mb-4">Bloqueios Ativos</h3>
                                
                                @php
                                    // Use the blocks passed from the controller
                                    $blocks = $upcomingBlocks ?? collect([]);
                                @endphp
                                
                                @if($blocks->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($blocks as $block)
                                        <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-4">
                                                    <div class="flex-shrink-0">
                                                        @if($block->type === 'full_day')
                                                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                                        @else
                                                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">
                                                            {{ \Carbon\Carbon::parse($block->date)->format('d/m/Y') }}
                                                            @if($block->type === 'time_range' && $block->start_time && $block->end_time)
                                                                - {{ \Carbon\Carbon::parse($block->start_time)->format('H:i') }} às {{ \Carbon\Carbon::parse($block->end_time)->format('H:i') }}
                                                            @else
                                                                - Dia Inteiro
                                                            @endif
                                                        </p>
                                                        @if($block->reason)
                                                            <p class="text-sm text-gray-500">{{ $block->reason }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0 flex items-center space-x-2">
                                                <!-- Edit Button -->
                                                <button onclick="editBlock({{ $block->id }}, '{{ $block->date }}', '{{ $block->type }}', '{{ $block->start_time }}', '{{ $block->end_time }}', '{{ $block->reason }}')" 
                                                        class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                
                                                <!-- Delete Button -->
                                                <form action="{{ route('admin.settings.schedule-blocks.destroy', $block->id) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('Tem certeza que deseja excluir este bloqueio?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Excluir">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-12 bg-gray-50 rounded-lg">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum bloqueio ativo</h3>
                                        <p class="mt-1 text-sm text-gray-500">Crie um bloqueio para bloquear datas ou horários específicos.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Backup Settings Tab -->
                    <div id="backup-tab" class="tab-content bg-white rounded-lg shadow hidden">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-6">Backup e Restore</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Backup -->
                                <div class="bg-blue-50 p-6 rounded-lg">
                                    <h3 class="text-lg font-medium text-blue-900 mb-4">Backup dos Dados</h3>
                                    <p class="text-sm text-blue-700 mb-4">Faça o download de todos os dados da clínica em formato JSON.</p>
                                    <button onclick="downloadBackup()" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out w-full">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Gerar Backup
                                    </button>
                                </div>

                                <!-- Restore -->
                                <div class="bg-yellow-50 p-6 rounded-lg">
                                    <h3 class="text-lg font-medium text-yellow-900 mb-4">Restaurar Dados</h3>
                                    <p class="text-sm text-yellow-700 mb-4">Restaure os dados a partir de um arquivo de backup.</p>
                                    <form action="{{ route('admin.settings.import') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-4">
                                            <input type="file" name="settings_file" accept=".json" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                        </div>
                                        <button type="submit" 
                                                class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out w-full"
                                                onclick="return confirm('Tem certeza? Esta ação substituirá todos os dados atuais.')">
                                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4 4m0 0l4-4m-4 4V4"></path>
                                            </svg>
                                            Restaurar Backup
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Backup History -->
                            <div class="mt-8">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Histórico de Backups</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-center text-gray-500 py-8">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">Nenhum backup encontrado</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

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

    // Tab functionality
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');

    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active classes
            tabLinks.forEach(l => {
                l.classList.remove('active', 'text-indigo-600', 'border-indigo-500');
                l.classList.add('text-gray-500', 'border-transparent');
            });
            tabContents.forEach(content => content.classList.add('hidden'));

            // Add active classes
            this.classList.add('active', 'text-indigo-600', 'border-indigo-500');
            this.classList.remove('text-gray-500', 'border-transparent');

            // Show corresponding tab content
            const tabId = this.getAttribute('href').substring(1) + '-tab';
            const targetTab = document.getElementById(tabId);
            if (targetTab) {
                targetTab.classList.remove('hidden');
            }
        });
    });

    // Validation for work hours
    const workStartTime = document.getElementById('work_start_time');
    const workEndTime = document.getElementById('work_end_time');
    const lunchStartTime = document.getElementById('lunch_start_time');
    const lunchEndTime = document.getElementById('lunch_end_time');
    
    function validateTimes() {
        if (workStartTime.value && workEndTime.value) {
            if (workStartTime.value >= workEndTime.value) {
                workEndTime.setCustomValidity('O horário de fim deve ser maior que o de início');
            } else {
                workEndTime.setCustomValidity('');
            }
        }
        
        if (lunchStartTime.value && lunchEndTime.value) {
            if (lunchStartTime.value >= lunchEndTime.value) {
                lunchEndTime.setCustomValidity('O horário de fim do almoço deve ser maior que o de início');
            } else {
                lunchEndTime.setCustomValidity('');
            }
        }
    }
    
    [workStartTime, workEndTime, lunchStartTime, lunchEndTime].forEach(input => {
        if (input) {
            input.addEventListener('change', validateTimes);
        }
    });

    // Initialize block type fields on page load
    if (document.getElementById('block_type')) {
        toggleTimeFields();
    }
});

function downloadBackup() {
    // Use the correct export route
    window.location.href = "{{ route('admin.settings.export') }}";
}

function toggleTimeFields() {
    const blockType = document.getElementById('block_type').value;
    const startTimeField = document.getElementById('start_time_field');
    const endTimeField = document.getElementById('end_time_field');
    const startTimeInput = document.getElementById('block_start_time');
    const endTimeInput = document.getElementById('block_end_time');
    
    if (blockType === 'time_range') {
        startTimeField.style.display = 'block';
        endTimeField.style.display = 'block';
        startTimeInput.required = true;
        endTimeInput.required = true;
    } else {
        startTimeField.style.display = 'none';
        endTimeField.style.display = 'none';
        startTimeInput.required = false;
        endTimeInput.required = false;
        startTimeInput.value = '';
        endTimeInput.value = '';
    }
}

function editBlock(id, date, type, startTime, endTime, reason) {
    // Create edit modal or form - for now, we'll use a simple prompt-based edit
    const newDate = prompt('Nova data (YYYY-MM-DD):', date);
    if (!newDate) return;
    
    const newType = confirm('Clique OK para "Horário Específico" ou Cancelar para "Dia Inteiro"') ? 'time_range' : 'full_day';
    let newStartTime = '';
    let newEndTime = '';
    
    if (newType === 'time_range') {
        newStartTime = prompt('Horário de início (HH:MM):', startTime || '');
        if (!newStartTime) return;
        
        newEndTime = prompt('Horário de fim (HH:MM):', endTime || '');
        if (!newEndTime) return;
    }
    
    const newReason = prompt('Motivo:', reason || '');
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/settings/schedule-blocks/${id}`;
    
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     '{{ csrf_token() }}';
    
    form.innerHTML = `
        <input type="hidden" name="_token" value="${csrfToken}">
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="date" value="${newDate}">
        <input type="hidden" name="type" value="${newType}">
        <input type="hidden" name="start_time" value="${newStartTime}">
        <input type="hidden" name="end_time" value="${newEndTime}">
        <input type="hidden" name="reason" value="${newReason}">
    `;
    
    document.body.appendChild(form);
    form.submit();
}
</script>
</body>
</html>