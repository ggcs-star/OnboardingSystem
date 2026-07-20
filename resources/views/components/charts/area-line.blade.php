@props(['data'])

@php
    $w = 600;
    $h = 220;
    $padTop = 16;
    $padBottom = 28;
    $padX = 10;
    $plotH = $h - $padTop - $padBottom;
    $max = max(collect($data)->max('value'), 1);
    $n = count($data);
    $stepX = $n > 1 ? ($w - $padX * 2) / ($n - 1) : 0;

    $points = collect($data)->values()->map(function ($row, $i) use ($padX, $stepX, $padTop, $plotH, $max) {
        $x = $padX + $i * $stepX;
        $y = $padTop + $plotH * (1 - ($row['value'] / $max));

        return ['x' => round($x, 1), 'y' => round($y, 1), 'label' => $row['label'], 'value' => $row['value']];
    });

    $linePoints = $points->map(fn ($p) => "{$p['x']},{$p['y']}")->implode(' ');
    $baseline = $h - $padBottom;
    $areaPath = "M {$padX},{$baseline} L {$linePoints} L " . ($w - $padX) . ",{$baseline} Z";
@endphp

<div>
    <svg viewBox="0 0 {{ $w }} {{ $h }}" class="w-full" preserveAspectRatio="none" style="height: 200px" role="img" aria-label="Line chart">
        <line x1="{{ $padX }}" y1="{{ $baseline }}" x2="{{ $w - $padX }}" y2="{{ $baseline }}" stroke="rgb(var(--color-border))" stroke-width="1" />

        <path d="{{ $areaPath }}" fill="rgb(var(--color-primary) / 0.1)" />

        <polyline points="{{ $linePoints }}" fill="none" stroke="rgb(var(--color-primary))" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" />

        @foreach ($points as $p)
            <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="4" fill="rgb(var(--color-primary))" stroke="white" stroke-width="2">
                <title>{{ $p['label'] }}: {{ $p['value'] }}</title>
            </circle>
        @endforeach

        @if ($points->isNotEmpty())
            @php $last = $points->last(); @endphp
            <text x="{{ $last['x'] }}" y="{{ $last['y'] - 12 }}" text-anchor="end" font-size="13" font-weight="600" fill="rgb(var(--color-secondary-dark))">{{ $last['value'] }}</text>
        @endif
    </svg>

    <div class="mt-1 flex text-[11px] text-secondary" style="padding: 0 {{ round($padX / $w * 100, 1) }}%">
        @foreach ($points as $p)
            <span class="flex-1 text-center">{{ $p['label'] }}</span>
        @endforeach
    </div>
</div>
