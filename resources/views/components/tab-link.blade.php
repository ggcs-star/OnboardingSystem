@props(['href', 'active' => false, 'icon' => null])

<a href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-3 py-3 text-sm font-medium transition '
            . ($active
                ? 'border-primary text-primary'
                : 'border-transparent text-secondary hover:text-secondary-dark hover:border-app-border'),
    ]) }}
>
    @if ($icon)
        <x-icon :name="$icon" class="w-4 h-4" />
    @endif
    {{ $slot }}
</a>
