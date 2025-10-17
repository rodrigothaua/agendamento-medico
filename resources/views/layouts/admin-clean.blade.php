<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - <span class="clinic-title">Clínica Saúde</span></title>
    
    <!-- Scripts e CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/settings-api.js') }}"></script>
    <script src="{{ asset('js/admin-utils.js') }}"></script>
    
    <!-- Estilos base -->
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
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar Component -->
        <x-admin-sidebar :activeRoute="request()->route()->getName()" />
        
        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">
            <!-- Mobile Header -->
            <header class="lg:hidden bg-white shadow-sm border-b border-gray-200 px-4 py-3">
                <div class="flex items-center justify-between">
                    <button id="mobile-menu-button" class="text-gray-600 hover:text-gray-900 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-900">@yield('title')</h1>
                    <div></div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Toast Notifications -->
    <x-toast />

    <!-- Global Script -->
    <script>
        // Inicializar o nome da clínica quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            updateClinicName();
        });
    </script>

    @stack('scripts')
</body>
</html>