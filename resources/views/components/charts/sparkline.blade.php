@props(['data', 'color' => 'rgb(var(--color-primary))'])

@php
    $values = collect($data)->values();
    $max = $values->max() ?: 1;
    $n = $values->count();
    $w = 72;
    $h = 28;
    $stepX = $n > 1 ? $w / ($n - 1) : 0;

    $points = $values->map(function ($value, $i) use ($stepX, $h, $max) {
        $x = round($i * $stepX, 1);
        $y = round($h - 2 - ($value / $max) * ($h - 4), 1);

        return "{$x},{$y}";
    })->implode(' ');
@endphp

<svg viewBox="0 0 {{ $w }} {{ $h }}" width="{{ $w }}" height="{{ $h }}" role="img" aria-label="Trend">
    <polyline points="{{ $points }}" fill="none" stroke="{{ $color }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <title>{{ $values->implode(', ') }}</title>
    </polyline>
</svg>
