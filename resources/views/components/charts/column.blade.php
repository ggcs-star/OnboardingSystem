@props(['data', 'unit' => ''])

@php
    $barWidth = 48;
    $gap = 28;
    $padLeft = 36;
    $padRight = 16;
    $padTop = 20;
    $padBottom = 46;
    $plotH = 160;
    $h = $padTop + $plotH + $padBottom;

    $n = count($data);
    $w = $padLeft + $padRight + max($n, 1) * $barWidth + max($n - 1, 0) * $gap;

    $max = collect($data)->max('value') ?: 1;
    $ticks = collect([0, 0.25, 0.5, 0.75, 1])->map(fn ($f) => (int) ceil($max * $f))->unique()->sortDesc()->values();
@endphp

<div class="overflow-x-auto">
    <svg viewBox="0 0 {{ $w }} {{ $h }}" width="{{ $w }}" style="min-width: 100%; height: {{ $h }}px" role="img" aria-label="Bar chart">
        @foreach ($ticks as $tick)
            @php $y = $padTop + $plotH * (1 - ($max > 0 ? $tick / $max : 0)); @endphp
            <line x1="{{ $padLeft }}" y1="{{ $y }}" x2="{{ $w - $padRight }}" y2="{{ $y }}" stroke="rgb(var(--color-border))" stroke-width="1" stroke-dasharray="{{ $tick === 0 ? '0' : '3,3' }}" />
            <text x="{{ $padLeft - 8 }}" y="{{ $y + 3 }}" text-anchor="end" font-size="10" fill="rgb(var(--color-secondary))">{{ $tick }}</text>
        @endforeach

        @foreach ($data as $i => $row)
            @php
                $barH = $max > 0 ? round(($row['value'] / $max) * $plotH) : 0;
                $x = $padLeft + $i * ($barWidth + $gap);
                $y = $padTop + $plotH - $barH;
                $labelX = $x + $barWidth / 2;
                $labelY = $padTop + $plotH + 14;
            @endphp
            <rect
                x="{{ $x }}" y="{{ $y }}" width="{{ $barWidth }}" height="{{ max($barH, 2) }}" rx="4"
                fill="{{ $row['color'] ?? 'rgb(var(--color-primary))' }}"
                class="transition-opacity hover:opacity-80"
            ><title>{{ $row['label'] }}: {{ $row['value'] }}{{ $unit }}</title></rect>

            @if ($row['value'] > 0)
                <text x="{{ $labelX }}" y="{{ $y - 6 }}" text-anchor="middle" font-size="10" font-weight="600" fill="rgb(var(--color-secondary-dark))">{{ $row['value'] }}{{ $unit }}</text>
            @endif

            <text
                x="{{ $labelX }}" y="{{ $labelY }}" text-anchor="end" font-size="10" fill="rgb(var(--color-secondary))"
                transform="rotate(-40 {{ $labelX }} {{ $labelY }})"
            >{{ \Illuminate\Support\Str::limit($row['label'], 14) }}</text>
        @endforeach

        <line x1="{{ $padLeft }}" y1="{{ $padTop + $plotH }}" x2="{{ $w - $padRight }}" y2="{{ $padTop + $plotH }}" stroke="rgb(var(--color-border))" stroke-width="1.5" />
    </svg>
</div>
