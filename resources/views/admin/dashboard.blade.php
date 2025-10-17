<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Clínica Saúde</title>
    <!-- Inclua o Tailwind CSS. Assumindo que você usa um CDN ou o seu build local. -->
    <script src="https://cdn.tailwindcss.com"></script>
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
    </style>
</head>
<body>

<div class="min-h-screen p-8">
    <header class="flex justify-between items-center mb-10 border-b pb-4">
        <div>
            <h1 class="text-4xl font-extrabold text-indigo-700">Painel Administrativo</h1>
            <!-- Exibe o nome do usuário logado na sessão -->
            <p class="text-gray-500 mt-1">Bem-vindo(a), {{ session('user_name') ?? 'Médico' }}.</p>
        </div>
        
        <!-- Formulário de Logout -->
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" 
                    class="flex items-center space-x-2 px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-2 0V4H5v12h12v-2a1 1 0 112 0v3a2 2 0 01-2 2H4a2 2 0 01-2-2V4a2 2 0 012-2h12a1 1 0 110 2H4zm8 10a1 1 0 10-1-1v-4a1 1 0 10-2 0v4a1 1 0 102 0v-4z" clip-rule="evenodd" />
                </svg>
                <span>Sair</span>
            </button>
        </form>
    </header>

    <!-- Estatísticas Rápidas (Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">

        <!-- Card 1: Agendamentos Confirmados -->
        <div class="card bg-white p-6 border-l-4 border-green-500">
            <p class="text-sm font-medium text-gray-500 uppercase">Confirmados Totais</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['confirmed'], 0, ',', '.') }}</p>
        </div>

        <!-- Card 2: Agendamentos Pendentes -->
        <div class="card bg-white p-6 border-l-4 border-yellow-500">
            <p class="text-sm font-medium text-gray-500 uppercase">Pendentes (Reserva)</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['pending'], 0, ',', '.') }}</p>
        </div>

        <!-- Card 3: Pacientes Registrados -->
        <div class="card bg-white p-6 border-l-4 border-indigo-500">
            <p class="text-sm font-medium text-gray-500 uppercase">Pacientes Registrados</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['patients'], 0, ',', '.') }}</p>
        </div>

        <!-- Card 4: Confirmados Hoje -->
        <div class="card bg-white p-6 border-l-4 border-blue-500">
            <p class="text-sm font-medium text-gray-500 uppercase">Agendamentos Confirmados Hoje</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['today_confirmed'], 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Tabela de Agendamentos Recentes -->
    <div class="bg-white card p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-3">Agendamentos Recentes</h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CPF / Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data/Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($appointments as $appointment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $appointment->patient->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $appointment->patient->cpf ?? 'N/A' }}<br>
                                <span class="text-xs text-indigo-500">{{ $appointment->patient->email ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClass = [
                                        'confirmed' => 'bg-green-100 text-green-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'canceled' => 'bg-red-100 text-red-800',
                                    ][$appointment->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($appointment->payment->amount ?? 0, 2, ',', '.') }} R$
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum agendamento encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
