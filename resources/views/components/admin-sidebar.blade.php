@props(['activeRoute' => '', 'stats' => []])

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
            <h2 id="clinic-name" class="text-lg md:text-xl font-bold text-gray-800"></h2>
        </div>
        <!-- Close button for mobile -->
        <button id="close-sidebar" class="md:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    <nav class="mt-4 md:mt-6 flex-1 px-2 md:px-4 space-y-1 md:space-y-2">
        <div class="space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ $activeRoute === 'admin.dashboard' ? 'active' : '' }} flex items-center px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-gray-700 rounded-lg font-medium">
                <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 md:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2V7"></path>
                </svg>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('admin.appointments.index') }}" class="sidebar-link {{ str_starts_with($activeRoute, 'admin.appointments') ? 'active' : '' }} flex items-center px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-gray-700 rounded-lg font-medium">
                <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 md:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Agendamentos</span>
                @if(isset($stats['pending']) && $stats['pending'] > 0)
                    <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        {{ $stats['pending'] }}
                    </span>
                @endif
            </a>
            
            <a href="{{ route('admin.patients.index') }}" class="sidebar-link {{ str_starts_with($activeRoute, 'admin.patients') ? 'active' : '' }} flex items-center px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-gray-700 rounded-lg font-medium">
                <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 md:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                </svg>
                <span>Pacientes</span>
                @if(isset($stats['patients']) && $stats['patients'] > 0)
                    <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ $stats['patients'] }}
                    </span>
                @elseif(isset($stats['total']) && $stats['total'] > 0)
                    <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        {{ $stats['total'] }}
                    </span>
                @endif
            </a>
            
            <a href="{{ route('admin.payments.index') }}" class="sidebar-link {{ str_starts_with($activeRoute, 'admin.payments') ? 'active' : '' }} flex items-center px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-gray-700 rounded-lg font-medium">
                <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 md:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Pagamentos</span>
                @if(str_starts_with($activeRoute, 'admin.payments') && isset($stats['total']) && $stats['total'] > 0)
                    <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        {{ $stats['total'] }}
                    </span>
                @endif
            </a>
            
            <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ str_starts_with($activeRoute, 'admin.reports') ? 'active' : '' }} flex items-center px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-gray-700 rounded-lg font-medium">
                <svg class="w-4 h-4 md:w-5 md:h-5 mr-2 md:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span>Relatórios</span>
            </a>
            
            <a href="{{ route('admin.settings') }}" class="sidebar-link {{ str_starts_with($activeRoute, 'admin.settings') ? 'active' : '' }} flex items-center px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-gray-700 rounded-lg font-medium">
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

<style>
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
        z-index: 50;
    }
    .sidebar.open {
        transform: translateX(0);
    }
}
.mobile-overlay {
    backdrop-filter: blur(2px);
}
.sidebar-link {
    transition: all 0.3s ease;
}
.sidebar-link:hover {
    background-color: rgba(99, 102, 241, 0.1);
    border-left: 4px solid #6366f1;
}
.sidebar-link.active {
    background-color: rgba(99, 102, 241, 0.1);
    border-left: 4px solid #6366f1;
    color: #6366f1;
}
</style>

