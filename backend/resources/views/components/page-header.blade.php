@props(['title' => '', 'subtitle' => '', 'breadcrumbs' => []])

<div class="bg-white shadow-sm border-b border-gray-200 mb-6">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        @if(count($breadcrumbs) > 0)
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    @foreach($breadcrumbs as $index => $breadcrumb)
                        <li class="inline-flex items-center">
                            @if($index > 0)
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                            @if($loop->last)
                                <span class="text-gray-500">{{ $breadcrumb['name'] }}</span>
                            @else
                                <a href="{{ $breadcrumb['url'] }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $breadcrumb['name'] }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif
        
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
                @if($subtitle)
                    <p class="text-sm text-gray-600 mt-1">{{ $subtitle }}</p>
                @endif
            </div>
            
            @if(isset($actions))
                <div class="flex space-x-3">
                    {{ $actions }}
                </div>
            @endif
        </div>
    </div>
</div>