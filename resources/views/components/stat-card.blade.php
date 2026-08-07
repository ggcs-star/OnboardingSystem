@props(['label', 'value', 'hint' => null, 'tint' => null, 'icon' => null, 'badge' => null, 'compact' => false, 'wave' => false])

@php
    $styles = match ($tint) {
        'primary' => ['bg' => 'bg-primary-light border-primary/20', 'icon' => 'bg-primary text-white', 'wave' => 'text-primary'],
        'success' => ['bg' => 'bg-success-light border-success/20', 'icon' => 'bg-success text-white', 'wave' => 'text-success'],
        'warning' => ['bg' => 'bg-warning-light border-warning/20', 'icon' => 'bg-warning text-white', 'wave' => 'text-warning'],
        'danger' => ['bg' => 'bg-danger-light border-danger/20', 'icon' => 'bg-danger text-white', 'wave' => 'text-danger'],
        'teal' => ['bg' => 'bg-chart-5/15 border-chart-5/25', 'icon' => 'bg-chart-5 text-white', 'wave' => 'text-chart-5'],
        'violet' => ['bg' => 'bg-chart-3/15 border-chart-3/25', 'icon' => 'bg-chart-3 text-white', 'wave' => 'text-chart-3'],
        default => ['bg' => 'bg-white border-app-border', 'icon' => 'bg-secondary-light text-secondary', 'wave' => 'text-secondary'],
    };
@endphp

<div {{ $attributes->merge(['class' => "relative overflow-hidden rounded-xl border shadow-sm transition hover:shadow-md {$styles['bg']} " . ($compact ? 'p-3' : 'p-5')]) }}>
    @if ($wave)
        <svg class="pointer-events-none absolute inset-x-0 bottom-0 h-10 w-full {{ $styles['wave'] }} opacity-[0.15]" viewBox="0 0 400 40" preserveAspectRatio="none" fill="currentColor">
            <path d="M0 22 Q 50 2, 100 22 T 200 22 T 300 22 T 400 22 V40 H0 Z" />
        </svg>
    @endif

    <div class="relative">
        <div class="flex items-start justify-between gap-2">
            <p class="text-xs font-medium uppercase tracking-wide text-secondary">{{ $label }}</p>
            @if ($badge)
                <span class="shrink-0 rounded-full bg-secondary-light px-2 py-0.5 text-xs font-semibold text-secondary-dark">{{ $badge }}</span>
            @elseif ($icon)
                <span class="flex {{ $compact ? 'h-7 w-7' : ($wave ? 'h-11 w-11' : 'h-8 w-8') }} shrink-0 items-center justify-center {{ $wave ? 'rounded-xl' : 'rounded-lg' }} {{ $styles['icon'] }}">
                    <x-icon :name="$icon" class="{{ $wave ? 'w-5 h-5' : 'w-4 h-4' }}" />
                </span>
            @endif
        </div>
        <p class="mt-2 {{ $compact ? 'text-xl' : 'text-3xl' }} font-semibold text-secondary-dark">{{ $value }}</p>
        @if ($hint)
            <p class="mt-1 text-xs text-secondary">{{ $hint }}</p>
        @endif
    </div>
</div>
