@props(['href', 'active' => false, 'icon' => null, 'badge' => null])

<a href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'group relative flex items-center justify-between gap-3 rounded-lg px-3 py-2 text-sm font-medium transition '
            . ($active
                ? 'bg-sidebar-active-bg text-sidebar-active-text shadow-sm before:absolute before:-left-3 before:top-1/2 before:h-5 before:w-1 before:-translate-y-1/2 before:rounded-r-full before:bg-primary'
                : 'text-sidebar-text hover:bg-white hover:text-sidebar-text'),
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
