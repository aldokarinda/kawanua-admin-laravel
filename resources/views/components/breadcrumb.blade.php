{{--
 * Copyright (c) 2026 Aldo Karinda, UNKLAB Business School.
 * All rights reserved.
 * Licensed under the Non-Commercial Software License Agreement.
--}}
@props(['items' => []])

@if(count($items) > 0)
<nav class="flex mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-2 text-sm">
        @foreach($items as $index => $item)
            <li class="inline-flex items-center">
                @if($index > 0)
                    <svg class="w-4 h-4 text-gray-400 dark:text-slate-500 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                @endif

                @if($loop->last)
                    <span class="font-semibold text-gray-900 dark:text-slate-200">{{ $item['label'] }}</span>
                @else
                    <a href="{{ $item['url'] ?? '#' }}" class="text-gray-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                        {{ $item['label'] }}
                    </a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif
