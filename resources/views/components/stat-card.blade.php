@props(['label', 'value', 'hint' => null, 'tint' => null, 'icon' => null])

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

<div {{ $attributes->merge(['class' => "rounded-xl border {$styles['bg']} p-5"]) }}>
    <div class="flex items-start justify-between">
        <p class="text-xs font-medium uppercase tracking-wide text-secondary">{{ $label }}</p>
        @if ($icon)
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $styles['icon'] }}">
                <x-icon :name="$icon" class="w-4 h-4" />
            </span>
        @endif
    </div>
    <p class="mt-2 text-3xl font-semibold text-secondary-dark">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs text-secondary">{{ $hint }}</p>
    @endif
</div>
