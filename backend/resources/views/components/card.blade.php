@props(['title' => '', 'icon' => '', 'padding' => 'p-6'])

<div {{ $attributes->merge(['class' => "card bg-white $padding"]) }}>
    @if($title || $icon)
        <div class="flex items-center mb-4">
            @if($icon)
                <i class="{{ $icon }} text-indigo-600 text-xl mr-3"></i>
            @endif
            @if($title)
                <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
            @endif
        </div>
    @endif
    
    {{ $slot }}
</div>