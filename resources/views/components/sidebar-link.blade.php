@props(['href', 'active' => false, 'icon' => null, 'badge' => null])

<a href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'group flex items-center justify-between gap-3 rounded-lg px-3 py-2 text-sm font-medium transition '
            . ($active
                ? 'bg-sidebar-active-bg text-sidebar-active-text'
                : 'text-sidebar-text hover:bg-surface-alt hover:text-secondary-dark'),
    ]) }}
>
    <span class="flex items-center gap-3">
        @if ($icon)
            <x-icon :name="$icon" class="w-[18px] h-[18px] shrink-0" />
        @endif
        <span>{{ $slot }}</span>
    </span>

    @if ($badge)
        <span class="flex h-5 min-w-[20px] items-center justify-center rounded-full bg-danger px-1.5 text-[11px] font-semibold text-white">
            {{ $badge }}
        </span>
    @endif
</a>
