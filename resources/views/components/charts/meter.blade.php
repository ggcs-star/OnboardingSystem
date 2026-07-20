@props(['label', 'done', 'total', 'icon' => null])

@php
    $pct = $total > 0 ? round(($done / $total) * 100) : 0;
    $isComplete = $total > 0 && $done === $total;
    $fill = $isComplete ? 'bg-success' : 'bg-primary';
    $track = $isComplete ? 'bg-success-light' : 'bg-primary-light';
@endphp

<div>
    <div class="mb-1.5 flex items-center justify-between text-xs">
        <span class="flex items-center gap-1.5 font-medium text-secondary-dark">
            @if ($icon)
                <x-icon :name="$icon" class="w-3.5 h-3.5 text-secondary" />
            @endif
            {{ $label }}
        </span>
        <span class="tabular-nums text-secondary">{{ $done }}/{{ $total }} · {{ $pct }}%</span>
    </div>
    <div
        class="h-2.5 w-full overflow-hidden rounded-full {{ $track }}"
        title="{{ $label }}: {{ $pct }}% complete ({{ $done }}/{{ $total }})"
        tabindex="0"
    >
        <div class="h-2.5 rounded-full {{ $fill }} transition-all duration-300 ease-out" style="width: {{ max($pct, $total > 0 ? 2 : 0) }}%"></div>
    </div>
</div>
