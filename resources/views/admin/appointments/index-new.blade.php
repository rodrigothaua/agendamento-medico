@extends('layouts.admin')

@section('title', 'Agendamentos')

@section('content')
<div class="p-6">
    <!-- Header -->
    <x-page-header 
        title="Agendamentos" 
        subtitle="Gerencie todos os agendamentos da clínica"
    >
        <x-slot name="actions">
            <x-button 
                variant="primary" 
                icon="fas fa-plus"
                onclick="window.location.href='{{ route('admin.appointments.create') }}'"
            >
                Novo Agendamento
            </x-button>
        </x-slot>
    </x-page-header>

    <!-- Alerts -->
    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <!-- Filtros -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.appointments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-input 
                name="search" 
                placeholder="Buscar por paciente..." 
                :value="request('search')"
                icon="fas fa-search"
            />
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Data</label>
                <input 
                    type="date" 
                    name="date" 
                    value="{{ request('date') }}"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todos</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmado</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Concluído</option>
                    <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <x-button type="submit" variant="primary" icon="fas fa-filter">
                    Filtrar
                </x-button>
                <x-button 
                    type="button" 
                    variant="outline"
                    onclick="window.location.href='{{ route('admin.appointments.index') }}'"
                >
                    Limpar
                </x-button>
            </div>
        </form>
    </x-card>

    <!-- Tabela de Agendamentos -->
    <x-card>
        @if(isset($appointments) && count($appointments) > 0)
            <x-table :headers="['Paciente', 'Data/Hora', 'Telefone', 'Status', 'Ações']">
                @foreach($appointments as $appointment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-medium text-sm">
                                    {{ substr($appointment->patient->name ?? 'P', 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $appointment->patient->name ?? 'Nome não informado' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $appointment->patient->email ?? 'Email não informado' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $appointment->patient->phone ?? 'Não informado' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if($appointment->status == 'confirmed') bg-green-100 text-green-800
                                @elseif($appointment->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($appointment->status == 'completed') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                @if($appointment->status !== 'completed')
                                    <select onchange="updateStatus({{ $appointment->id }}, this.value)" 
                                            class="text-xs border-gray-300 rounded">
                                        <option value="">Ação</option>
                                        @if($appointment->status !== 'confirmed')
                                            <option value="confirmed">Confirmar</option>
                                        @endif
                                        @if($appointment->status !== 'canceled')
                                            <option value="canceled">Cancelar</option>
                                        @endif
                                        @if($appointment->status !== 'completed')
                                            <option value="completed">Concluir</option>
                                        @endif
                                    </select>
                                @endif
                                
                                <x-button 
                                    size="sm" 
                                    variant="outline"
                                    onclick="window.location.href='{{ route('admin.appointments.show', $appointment->id) }}'"
                                >
                                    Ver
                                </x-button>
                                
                                <x-button 
                                    size="sm" 
                                    variant="danger"
                                    onclick="confirmDelete({{ $appointment->id }}, '{{ $appointment->patient->name }}', '{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d/m/Y H:i') }}')"
                                >
                                    <i class="fas fa-trash"></i>
                                </x-button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-table>
            
            <!-- Paginação -->
            @if(method_exists($appointments, 'links'))
                <div class="mt-6">
                    {{ $appointments->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <i class="fas fa-calendar-times text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum agendamento encontrado</h3>
                <p class="text-gray-500 mb-4">Não há agendamentos cadastrados ainda.</p>
                <x-button 
                    variant="primary" 
                    icon="fas fa-plus"
                    onclick="window.location.href='{{ route('admin.appointments.create') }}'"
                >
                    Criar Primeiro Agendamento
                </x-button>
            </div>
        @endif
    </x-card>
</div>

<!-- Forms ocultos para ações -->
<form id="status-form" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" id="status-input">
</form>

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function updateStatus(appointmentId, status) {
    if (!status) return;
    
    const statusTexts = {
        'confirmed': 'confirmar',
        'canceled': 'cancelar',
        'completed': 'concluir'
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
@endpush