<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Admin - <span id="clinic-title">Clínica Saúde</span></title>
    <!-- Inclua o Tailwind CSS. Assumindo que você usa um CDN ou o seu build local. -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/settings-api.js') }}"></script>
    <style>
        /* Estilo para a fonte Inter */
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
        /* Overlay */
        .mobile-overlay {
            backdrop-filter: blur(2px);
        }
    </style>
</head>
<body>

<div class="flex min-h-screen bg-gray-50">
    <!-- Mobile menu overlay -->
    <!-- Sidebar Component -->
    <x-admin-sidebar :activeRoute="'admin.dashboard'" :stats="$stats ?? []" />

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
                            <h1 class="text-xl md:text-2xl font-bold text-gray-900">Dashboard</h1>
                            <p class="text-xs md:text-sm text-gray-600 hidden sm:block">Visão geral dos agendamentos e atividades</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-xs md:text-sm text-gray-500 hidden sm:block">
                            {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
            <div class="max-w-7xl mx-auto py-4 md:py-6 px-4 sm:px-6 lg:px-8">

                <!-- Estatísticas Rápidas (Cards) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">

                    <!-- Card 1: Agendamentos Confirmados -->
                    <div class="card bg-white p-4 md:p-6 border-l-4 border-green-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-6 h-6 md:w-8 md:h-8 bg-green-500 rounded-md flex items-center justify-center">
                                    <svg class="w-3 h-3 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 md:ml-4">
                                <p class="text-xs md:text-sm font-medium text-gray-500 uppercase">Confirmados</p>
                                <p class="text-lg md:text-2xl font-bold text-gray-900">{{ number_format($stats['confirmed'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Agendamentos Pendentes -->
                    <div class="card bg-white p-4 md:p-6 border-l-4 border-yellow-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-6 h-6 md:w-8 md:h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                    <svg class="w-3 h-3 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 md:ml-4">
                                <p class="text-xs md:text-sm font-medium text-gray-500 uppercase">Pendentes</p>
                                <p class="text-lg md:text-2xl font-bold text-gray-900">{{ number_format($stats['pending'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Pacientes Registrados -->
                    <div class="card bg-white p-4 md:p-6 border-l-4 border-indigo-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-6 h-6 md:w-8 md:h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                                    <svg class="w-3 h-3 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 md:ml-4">
                                <p class="text-xs md:text-sm font-medium text-gray-500 uppercase">Pacientes</p>
                                <p class="text-lg md:text-2xl font-bold text-gray-900">{{ number_format($stats['patients'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Confirmados Hoje -->
                    <div class="card bg-white p-4 md:p-6 border-l-4 border-blue-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-6 h-6 md:w-8 md:h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                    <svg class="w-3 h-3 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 md:ml-4">
                                <p class="text-xs md:text-sm font-medium text-gray-500 uppercase">Hoje</p>
                                <p class="text-lg md:text-2xl font-bold text-gray-900">{{ number_format($stats['today_confirmed'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Agendamentos Recentes -->
                <div class="bg-white card p-4 md:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 md:mb-6">
                        <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-3 sm:mb-0">Agendamentos Recentes</h2>
                        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                            <button class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                <span class="hidden sm:inline">Filtrar</span>
                                <span class="sm:hidden">Filtros</span>
                            </button>
                            <button class="inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span class="hidden sm:inline">Novo Agendamento</span>
                                <span class="sm:hidden">Novo</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto -mx-4 sm:mx-0">
                        <div class="inline-block min-w-full align-middle">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">CPF / Email</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data/Hora</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Valor</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($appointments as $appointment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 md:h-10 md:w-10">
                                                    <div class="h-8 w-8 md:h-10 md:w-10 rounded-full bg-indigo-500 flex items-center justify-center">
                                                        <span class="text-xs md:text-sm font-medium text-white">
                                                            {{ substr($appointment->patient->name ?? 'N', 0, 1) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-3 md:ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ Str::limit($appointment->patient->name ?? 'N/A', 20) }}</div>
                                                    <div class="text-xs md:text-sm text-gray-500 sm:hidden">{{ $appointment->patient->phone ?? 'Sem telefone' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                            <div>{{ $appointment->patient->cpf ?? 'N/A' }}</div>
                                            <div class="text-xs text-indigo-500">{{ Str::limit($appointment->patient->email ?? 'N/A', 25) }}</div>
                                        </td>
                                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d/m/Y') }}</div>
                                            <div class="text-xs md:text-sm text-gray-500">{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('H:i') }}</div>
                                        </td>
                                        <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusConfig = [
                                                    'confirmed' => ['class' => 'bg-green-100 text-green-800', 'text' => 'Confirmado'],
                                                    'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'text' => 'Pendente'],
                                                    'canceled' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Cancelado'],
                                                ];
                                                $config = $statusConfig[$appointment->status] ?? ['class' => 'bg-gray-100 text-gray-800', 'text' => ucfirst($appointment->status)];
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $config['class'] }}">
                                                <span class="hidden sm:inline">{{ $config['text'] }}</span>
                                                <span class="sm:hidden">{{ substr($config['text'], 0, 4) }}</span>
                                            </span>
                                        </td>
                                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                            R$ {{ number_format($appointment->payment->amount ?? 0, 2, ',', '.') }}
                                        </td>
                                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-1 md:space-x-2">
                                                <button class="text-indigo-600 hover:text-indigo-900 p-1">
                                                    <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </button>
                                                <button class="text-green-600 hover:text-green-900 p-1 hidden sm:block">
                                                    <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                @if($appointment->status !== 'canceled')
                                                <button class="text-red-600 hover:text-red-900 p-1 hidden sm:block">
                                                    <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                                @endif
                                                <!-- Menu dropdown para mobile -->
                                                <div class="sm:hidden relative">
                                                    <button class="text-gray-400 hover:text-gray-600 p-1">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 md:px-6 py-12 text-center text-gray-500">
                                            <svg class="mx-auto h-8 w-8 md:h-12 md:w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum agendamento</h3>
                                            <p class="mt-1 text-sm text-gray-500">Nenhum agendamento encontrado no momento.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
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
        document.body.classList.toggle('overflow-hidden'); // Prevent scroll when menu is open
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

    // Load clinic name dynamically
    loadClinicName();
});

// Function to load clinic name from API
async function loadClinicName() {
    try {
        const clinicInfo = await settingsAPI.getClinicInfo();
        
        // Update clinic name in sidebar
        const clinicNameElement = document.getElementById('clinic-name');
        if (clinicNameElement && clinicInfo.name) {
            clinicNameElement.textContent = clinicInfo.name;
        }
        
        // Update page title
        updatePageTitle(clinicInfo.name);
        
    } catch (error) {
        console.error('Erro ao carregar nome da clínica:', error);
        // Keep default values if API fails
    }
}

// Function to update page title
function updatePageTitle(clinicName) {
    if (clinicName) {
        document.title = `Dashboard Admin - ${clinicName}`;
        
        // Also update the title span if it exists
        const titleSpan = document.getElementById('clinic-title');
        if (titleSpan) {
            titleSpan.textContent = clinicName;
        }
    }
}
</script>

</body>
</html>
