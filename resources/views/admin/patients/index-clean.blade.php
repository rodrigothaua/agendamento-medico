@extends('layouts.admin-clean')

@section('title', 'Pacientes')

@section('content')
<div class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-900">Pacientes</h1>
                <p class="text-xs md:text-sm text-gray-600 hidden sm:block">Gerencie todos os pacientes da clínica</p>
            </div>
            <x-button 
                variant="primary" 
                onclick="AdminUtils.redirect('{{ route('admin.patients.create') }}')"
            >
                Novo Paciente
            </x-button>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto py-4 md:py-6 px-4 sm:px-6 lg:px-8">
    <!-- Alerts -->
    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <!-- Filtros -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.patients.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                <input 
                    type="text" 
                    name="search" 
                    id="search"
                    placeholder="Nome, email ou telefone..." 
                    value="{{ request('search') }}"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
            </div>
            
            <div class="flex items-end space-x-2">
                <x-button type="submit" variant="primary" class="flex-1">
                    Filtrar
                </x-button>
                <x-button 
                    type="button" 
                    variant="outline"
                    onclick="window.location.href='{{ route('admin.patients.index') }}'"
                >
                    Limpar
                </x-button>
            </div>
        </form>
    </x-card>

    <!-- Tabela de Pacientes -->
    <x-card>
        @if(isset($patients) && count($patients) > 0)
            <x-table :headers="['Paciente', 'Email', 'Telefone', 'Cadastrado em', 'Ações']">
                @foreach($patients as $patient)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-medium text-sm">
                                    {{ substr($patient->name ?? 'P', 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $patient->name ?? 'Nome não informado' }}
                                    </div>
                                    @if($patient->cpf)
                                        <div class="text-sm text-gray-500">
                                            CPF: {{ $patient->cpf }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $patient->email ?? 'Não informado' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $patient->phone ?? 'Não informado' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $patient->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <x-button 
                                    size="sm" 
                                    variant="outline"
                                    onclick="AdminUtils.redirect('{{ route('admin.patients.show', $patient->id) }}')"
                                >
                                    Ver
                                </x-button>
                                
                                <x-button 
                                    size="sm" 
                                    variant="outline"
                                    onclick="AdminUtils.redirect('{{ route('admin.patients.edit', $patient->id) }}')"
                                >
                                    Editar
                                </x-button>
                                
                                <x-button 
                                    size="sm" 
                                    variant="danger"
                                    onclick="PatientManager.confirmDelete({{ $patient->id }}, '{{ $patient->name }}')"
                                >
                                    <i class="fas fa-trash"></i>
                                </x-button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-table>
            
            <!-- Paginação -->
            @if(method_exists($patients, 'links'))
                <div class="mt-6">
                    {{ $patients->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum paciente encontrado</h3>
                <p class="text-gray-500 mb-4">Não há pacientes cadastrados ainda.</p>
                <x-button 
                    variant="primary"
                    onclick="AdminUtils.redirect('{{ route('admin.patients.create') }}')"
                >
                    Cadastrar Primeiro Paciente
                </x-button>
            </div>
        @endif
    </x-card>
</div>

<!-- Form oculto para delete -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection