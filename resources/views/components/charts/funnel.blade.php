@props(['data'])

@php
    $max = collect($data)->max('value') ?: 1;
@endphp

<div class="space-y-3" role="table" aria-label="Funnel chart">
    @foreach ($data as $row)
        @php
            $pct = $max > 0 ? max(round(($row['value'] / $max) * 100), 6) : 6;
        @endphp
        <div class="flex items-center gap-3">
            <span class="w-28 shrink-0 truncate text-xs font-medium text-secondary-dark sm:w-32">{{ $row['label'] }}</span>
            <div class="relative h-9 flex-1 overflow-hidden rounded-lg bg-secondary-light">
                <div
                    class="absolute inset-y-0 left-0 rounded-lg {{ $row['color'] }} transition-all duration-300 ease-out hover:brightness-110"
                    style="width: {{ $pct }}%"
                    title="{{ $row['label'] }}: {{ $row['value'] }} project(s)"
                    tabindex="0"
                ></div>
            </div>
            <span class="w-8 shrink-0 text-right text-sm font-semibold tabular-nums text-secondary-dark">{{ $row['value'] }}</span>
        </div>
    @endforeach
</div>
