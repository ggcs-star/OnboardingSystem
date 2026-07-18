@props(['segments'])

@php
    $total = collect($segments)->sum('count') ?: 1;
@endphp

<div>
    <div class="flex h-4 w-full gap-0.5 overflow-hidden rounded-full bg-secondary-light">
        @foreach ($segments as $segment)
            @php $pct = round(($segment['count'] / $total) * 100); @endphp
            @if ($segment['count'] > 0)
                <div
                    class="h-4 {{ $segment['classes'] }}"
                    style="width: {{ $pct }}%"
                    title="{{ $segment['label'] }}: {{ $segment['count'] }} ({{ $pct }}%)"
                    tabindex="0"
                ></div>
            @endif
        @endforeach
    </div>

    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
        @foreach ($segments as $segment)
            <div class="flex items-center gap-2 text-xs">
                <span class="h-2.5 w-2.5 shrink-0 rounded-sm {{ $segment['classes'] }}"></span>
                <span class="text-secondary-dark">{{ $segment['label'] }}</span>
                <span class="ml-auto tabular-nums font-medium text-secondary-dark">{{ $segment['count'] }}</span>
            </div>
        @endforeach
    </div>
</div>
