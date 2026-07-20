@props(['segments', 'centerLabel' => 'total'])

@php
    $total = collect($segments)->sum('count');
    $cumulative = 0;
    $stops = [];

    foreach ($segments as $seg) {
        if ($seg['count'] <= 0) {
            continue;
        }
        $start = $total > 0 ? round($cumulative / $total * 360, 2) : 0;
        $cumulative += $seg['count'];
        $end = $total > 0 ? round($cumulative / $total * 360, 2) : 0;
        $stops[] = "rgb(var({$seg['var']})) {$start}deg {$end}deg";
    }

    $gradient = $stops ? implode(', ', $stops) : 'rgb(var(--color-secondary-light)) 0deg 360deg';
@endphp

<div class="flex flex-col items-center gap-6 sm:flex-row sm:justify-center">
    <div class="relative h-36 w-36 shrink-0 rounded-full" style="background: conic-gradient({{ $gradient }});">
        <div class="absolute inset-[10px] flex flex-col items-center justify-center rounded-full bg-white text-center">
            <span class="text-2xl font-semibold text-secondary-dark">{{ $total }}</span>
            <span class="text-xs text-secondary">{{ $centerLabel }}</span>
        </div>
    </div>

    <div class="w-full space-y-2 sm:w-auto sm:min-w-[180px]">
        @foreach ($segments as $seg)
            @php $pct = $total > 0 ? round($seg['count'] / $total * 100) : 0; @endphp
            <div class="flex items-center gap-2 text-xs">
                <span class="h-2.5 w-2.5 shrink-0 rounded-sm {{ $seg['swatchClass'] }}"></span>
                <span class="text-secondary-dark">{{ $seg['label'] }}</span>
                <span class="ml-auto tabular-nums font-medium text-secondary-dark">{{ $seg['count'] }} · {{ $pct }}%</span>
            </div>
        @endforeach
    </div>
</div>
