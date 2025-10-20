@extends('layouts.admin')

@section('title', 'Bloqueios de Agenda')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-6 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Bloqueios de Agenda</h1>
                <p class="text-gray-600">Gerencie períodos indisponíveis na agenda</p>
            </div>
            <a href="{{ route('admin.settings') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                <i class="fas fa-arrow-left mr-2"></i>
                Voltar para Configurações
            </a>
        </div>

        <!-- Success/Error Messages -->
        <!-- Feedback visual removido, apenas toast será exibido -->
        @include('components.toast')

        <!-- Create New Block Form -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Novo Bloqueio</h2>
            
            <form action="{{ route('admin.settings.schedule-blocks.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Date -->
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Data</label>
                        <input type="date" id="date" name="date" min="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               required>
                    </div>

                    <!-- Block Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Bloqueio</label>
                        <select id="type" name="type" onchange="toggleTimeFields()" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                required>
                            <option value="full_day">Dia todo</option>
                            <option value="time_range">Horário específico</option>
                        </select>
                    </div>

                    <!-- Start Time -->
                    <div id="start_time_field" style="display: none;">
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Horário Inicial</label>
                        <input type="time" id="start_time" name="start_time"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- End Time -->
                    <div id="end_time_field" style="display: none;">
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">Horário Final</label>
                        <input type="time" id="end_time" name="end_time"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Reason -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Motivo (opcional)</label>
                    <input type="text" id="reason" name="reason" 
                           placeholder="Ex: Férias, Congresso, Emergência..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                        <i class="fas fa-plus mr-2"></i>
                        Criar Bloqueio
                    </button>
                </div>
            </form>
        </div>

        <!-- Current Blocks -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Bloqueios Ativos</h2>
            </div>

            @if($blocks->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horário</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motivo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($blocks as $block)
                                <tr class="hover:bg-gray-50">
                                    <!-- Date -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($block->date)->format('d/m/Y') }}
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($block->date)->translatedFormat('l') }}
                                        </div>
                                    </td>

                                    <!-- Type -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $block->type === 'full_day' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $block->type === 'full_day' ? 'Dia todo' : 'Horário' }}
                                        </span>
                                    </td>

                                    <!-- Time -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($block->type === 'time_range')
                                            {{ $block->start_time }} - {{ $block->end_time }}
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>

                                    <!-- Reason -->
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs truncate">
                                            {{ $block->reason ?: '—' }}
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $blockDate = \Carbon\Carbon::parse($block->date);
                                            $now = \Carbon\Carbon::now();
                                            
                                            if ($blockDate->isToday()) {
                                                $status = 'Hoje';
                                                $statusClass = 'bg-blue-100 text-blue-800';
                                            } elseif ($blockDate->isFuture()) {
                                                $status = 'Agendado';
                                                $statusClass = 'bg-green-100 text-green-800';
                                            } else {
                                                $status = 'Passado';
                                                $statusClass = 'bg-gray-100 text-gray-800';
                                            }
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                            {{ $status }}
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <!-- Edit Button -->
                                            <button onclick="openEditModal({{ $block->id }}, '{{ $block->date }}', '{{ $block->type }}', '{{ $block->start_time }}', '{{ $block->end_time }}', '{{ $block->reason }}')"
                                                    class="text-blue-600 hover:text-blue-900 transition duration-150 ease-in-out">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <!-- Delete Button -->
                                            <button class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out" onclick="showConfirmDeleteBlockModal({{ $block->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $blocks->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">Nenhum bloqueio de agenda cadastrado</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmação de exclusão de bloqueio (reutilizável) -->
@include('components.confirm-dialog', [
    'dialogId' => 'confirmDeleteBlockModal',
    'title' => 'Confirmar Exclusão',
    'message' => 'Tem certeza que deseja remover este bloqueio?',
    'confirmText' => 'Excluir',
    'confirmColor' => 'red',
    'cancelText' => 'Cancelar',
])

<form id="delete-block-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function showConfirmDeleteBlockModal(blockId) {
    window.confirmDialog.open({
        id: 'confirmDeleteBlockModal',
        onConfirm: function() {
            const form = document.getElementById('delete-block-form');
            form.action = `/admin/settings/schedule-blocks/${blockId}`;
            form.submit();
            toast.success('Bloqueio removido com sucesso!', 5000);
        }
    });
}
// ...existing code...
</script>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $blocks->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">Nenhum bloqueio de agenda cadastrado</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Editar Bloqueio</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Date -->
                <div>
                    <label for="edit_date" class="block text-sm font-medium text-gray-700 mb-2">Data</label>
                    <input type="date" id="edit_date" name="date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           required>
                </div>

                <!-- Type -->
                <div>
                    <label for="edit_type" class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                    <select id="edit_type" name="type" onchange="toggleEditTimeFields()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            required>
                        <option value="full_day">Dia todo</option>
                        <option value="time_range">Horário específico</option>
                    </select>
                </div>

                <!-- Start Time -->
                <div id="edit_start_time_field" style="display: none;">
                    <label for="edit_start_time" class="block text-sm font-medium text-gray-700 mb-2">Horário Inicial</label>
                    <input type="time" id="edit_start_time" name="start_time"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- End Time -->
                <div id="edit_end_time_field" style="display: none;">
                    <label for="edit_end_time" class="block text-sm font-medium text-gray-700 mb-2">Horário Final</label>
                    <input type="time" id="edit_end_time" name="end_time"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Reason -->
                <div>
                    <label for="edit_reason" class="block text-sm font-medium text-gray-700 mb-2">Motivo</label>
                    <input type="text" id="edit_reason" name="reason"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleTimeFields() {
    const type = document.getElementById('type').value;
    const startField = document.getElementById('start_time_field');
    const endField = document.getElementById('end_time_field');
    const startInput = document.getElementById('start_time');
    const endInput = document.getElementById('end_time');
    
    if (type === 'time_range') {
        startField.style.display = 'block';
        endField.style.display = 'block';
        startInput.required = true;
        endInput.required = true;
    } else {
        startField.style.display = 'none';
        endField.style.display = 'none';
        startInput.required = false;
        endInput.required = false;
        startInput.value = '';
        endInput.value = '';
    }
}

function toggleEditTimeFields() {
    const type = document.getElementById('edit_type').value;
    const startField = document.getElementById('edit_start_time_field');
    const endField = document.getElementById('edit_end_time_field');
    const startInput = document.getElementById('edit_start_time');
    const endInput = document.getElementById('edit_end_time');
    
    if (type === 'time_range') {
        startField.style.display = 'block';
        endField.style.display = 'block';
        startInput.required = true;
        endInput.required = true;
    } else {
        startField.style.display = 'none';
        endField.style.display = 'none';
        startInput.required = false;
        endInput.required = false;
    }
}

function openEditModal(id, date, type, startTime, endTime, reason) {
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editForm').action = `/admin/settings/schedule-blocks/${id}`;
    
    document.getElementById('edit_date').value = date;
    document.getElementById('edit_type').value = type;
    document.getElementById('edit_start_time').value = startTime || '';
    document.getElementById('edit_end_time').value = endTime || '';
    document.getElementById('edit_reason').value = reason || '';
    
    toggleEditTimeFields();
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleTimeFields();
});
</script>
@endsection