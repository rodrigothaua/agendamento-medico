@props(['type' => 'info', 'message' => '', 'dismissible' => true])

@php
    $classes = [
        'success' => 'bg-green-50 border border-green-200 text-green-700',
        'error' => 'bg-red-50 border border-red-200 text-red-700',
        'warning' => 'bg-yellow-50 border border-yellow-200 text-yellow-700',
        'info' => 'bg-blue-50 border border-blue-200 text-blue-700'
    ];
    
    $icons = [
        'success' => 'fas fa-check-circle',
        'error' => 'fas fa-exclamation-triangle',
        'warning' => 'fas fa-exclamation-circle',
        'info' => 'fas fa-info-circle'
    ];
@endphp

<div class="mb-4 {{ $classes[$type] }} px-4 py-3 rounded-lg {{ $dismissible ? 'relative' : '' }}" 
     @if($dismissible) x-data="{ show: true }" x-show="show" x-transition @endif>
    <div class="flex items-center">
        <i class="{{ $icons[$type] }} mr-2"></i>
        <span>{{ $message ?: $slot }}</span>
        @if($dismissible)
            <button @click="show = false" class="ml-auto -mx-1.5 -my-1.5 rounded-lg p-1.5 inline-flex h-8 w-8 hover:bg-opacity-20 hover:bg-gray-600">
                <i class="fas fa-times text-sm"></i>
            </button>
        @endif
    </div>
</div>