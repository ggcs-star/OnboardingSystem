@props(['label', 'value', 'hint' => null, 'tint' => null, 'icon' => null, 'badge' => null, 'compact' => false])

@php
    $styles = match ($tint) {
        'primary' => ['bg' => 'bg-primary-light border-primary/20', 'icon' => 'bg-primary text-white'],
        'success' => ['bg' => 'bg-success-light border-success/20', 'icon' => 'bg-success text-white'],
        'warning' => ['bg' => 'bg-warning-light border-warning/20', 'icon' => 'bg-warning text-white'],
        'danger' => ['bg' => 'bg-danger-light border-danger/20', 'icon' => 'bg-danger text-white'],
        'teal' => ['bg' => 'bg-chart-5/15 border-chart-5/25', 'icon' => 'bg-chart-5 text-white'],
        'violet' => ['bg' => 'bg-chart-3/15 border-chart-3/25', 'icon' => 'bg-chart-3 text-white'],
        default => ['bg' => 'bg-white border-app-border', 'icon' => 'bg-secondary-light text-secondary'],
    };
@endphp

<div {{ $attributes->merge(['class' => "rounded-xl border {$styles['bg']} " . ($compact ? 'p-3' : 'p-5')]) }}>
    <div class="flex items-start justify-between gap-2">
        <p class="text-xs font-medium uppercase tracking-wide text-secondary">{{ $label }}</p>
        @if ($badge)
            <span class="shrink-0 rounded-full bg-secondary-light px-2 py-0.5 text-xs font-semibold text-secondary-dark">{{ $badge }}</span>
        @elseif ($icon)
            <span class="flex {{ $compact ? 'h-7 w-7' : 'h-8 w-8' }} shrink-0 items-center justify-center rounded-lg {{ $styles['icon'] }}">
                <x-icon :name="$icon" class="w-4 h-4" />
            </span>
        @endif
    </div>
    <p class="mt-2 {{ $compact ? 'text-xl' : 'text-3xl' }} font-semibold text-secondary-dark">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs text-secondary">{{ $hint }}</p>
    @endif
</div>
