<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Perfil - Clínica Saúde</title>
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
        .card:hover {
            transform: translateY(-2px);
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
    <!-- Sidebar Component -->
    <x-admin-sidebar :activeRoute="'admin.profile.edit'" />

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
                            <h1 class="text-xl md:text-2xl font-bold text-gray-900">Meu Perfil</h1>
                            <p class="text-xs md:text-sm text-gray-600 hidden sm:block">Gerencie suas informações pessoais e configurações de segurança</p>
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
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <strong>Erros encontrados:</strong>
                        </div>
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Profile Cards -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- User Info Card -->
                    <div class="lg:col-span-1">
                        <div class="card bg-white p-6">
                            <div class="text-center">
                                <div class="w-20 h-20 bg-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span class="text-white font-bold text-2xl">
                                        {{ substr($user->name, 0, 1) }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $user->email }}</p>
                                <p class="text-xs text-gray-500 mt-2">Administrador do Sistema</p>
                            </div>
                            
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="text-sm text-gray-600 space-y-2">
                                    <div class="flex justify-between">
                                        <span>Último acesso:</span>
                                        <span class="font-medium">{{ now()->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Conta criada:</span>
                                        <span class="font-medium">{{ $user->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Status:</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Ativo
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Forms Section -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- Personal Information Form -->
                        <div class="card bg-white p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-6">Informações Pessoais</h2>
                            
                            <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-6">
                                @csrf
                                @method('PUT')
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Name -->
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome Completo</label>
                                        <input type="text" id="name" name="name" 
                                               value="{{ old('name', $user->name) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-300 @enderror" 
                                               required>
                                        @error('name')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
                                        <input type="email" id="email" name="email" 
                                               value="{{ old('email', $user->email) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-300 @enderror" 
                                               required>
                                        @error('email')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
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

                        <!-- Password Change Form -->
                        <div class="card bg-white p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-6">Alterar Senha</h2>
                            
                            <form action="{{ route('admin.profile.update-password') }}" method="POST" class="space-y-6">
                                @csrf
                                @method('PUT')
                                
                                <div class="space-y-4">
                                    <!-- Current Password -->
                                    <div>
                                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Senha Atual</label>
                                        <input type="password" id="current_password" name="current_password" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('current_password') border-red-300 @enderror" 
                                               required>
                                        @error('current_password')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- New Password -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Nova Senha</label>
                                            <input type="password" id="password" name="password" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-300 @enderror" 
                                                   required>
                                            @error('password')
                                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nova Senha</label>
                                            <input type="password" id="password_confirmation" name="password_confirmation" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                                   required>
                                        </div>
                                    </div>

                                    <!-- Password Requirements -->
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-gray-800 mb-2">Requisitos da senha:</h4>
                                        <ul class="text-sm text-gray-600 space-y-1">
                                            <li class="flex items-center">
                                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Mínimo de 8 caracteres
                                            </li>
                                            <li class="flex items-center">
                                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Pelo menos uma letra maiúscula e minúscula
                                            </li>
                                            <li class="flex items-center">
                                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Pelo menos um número
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Save Button -->
                                <div class="pt-4">
                                    <button type="submit" 
                                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        Alterar Senha
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Security Info -->
                        <div class="card bg-blue-50 border border-blue-200 p-6">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-blue-600 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <div>
                                    <h3 class="text-lg font-medium text-blue-900 mb-2">Segurança da Conta</h3>
                                    <p class="text-blue-700 text-sm mb-4">
                                        Mantenha sua conta segura seguindo as melhores práticas:
                                    </p>
                                    <ul class="text-blue-700 text-sm space-y-2">
                                        <li class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Use senhas fortes e únicas
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Altere sua senha regularmente
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Não compartilhe suas credenciais
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Sempre faça logout ao sair
                                        </li>
                                    </ul>
                                </div>
                            </div>
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
        if (e.key === 'Escape' && overlay && !overlay.classList.contains('hidden')) {
            closeMobileMenu();
        }
    });

    // Password strength indicator
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            // Add password strength visual feedback here if needed
        });
    }

    // Load clinic name dynamically
    if (typeof settingsAPI !== 'undefined') {
        settingsAPI.getPublicConfig().then(config => {
            updateClinicName(config.clinic_name || 'Clínica');
        }).catch(error => {
            console.warn('Could not load clinic name:', error);
        });
    }
});
</script>
</body>
</html>