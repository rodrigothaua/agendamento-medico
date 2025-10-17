<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo</title>
    <!-- Inclua o Tailwind CSS. -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #eef2f9;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">

<div class="card w-full max-w-md p-8 space-y-6 bg-white border border-gray-200">
    <div class="text-center">
        <h2 class="text-3xl font-extrabold text-gray-900">Acesso Administrativo</h2>
        <p class="mt-2 text-sm text-gray-600">Entre com suas credenciais.</p>
    </div>

    @if($errors->any())
        <div class="p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form class="space-y-6" action="{{ route('login.post') }}" method="POST">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
            <input id="email" name="email" type="email" autocomplete="email" required 
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                   value="{{ old('email', 'medico@clinic.com') }}">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Senha:</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required 
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                   value="senha123">
        </div>

        <div>
            <button type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                Entrar
            </button>
        </div>
    </form>
</div>

</body>
</html>
