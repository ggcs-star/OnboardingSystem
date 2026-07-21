@props(['current' => 1])

@php
    $steps = [1 => 'Product', 2 => 'Documents', 3 => 'Subscription'];
@endphp

<div {{ $attributes->merge(['class' => 'mx-auto flex w-full max-w-md items-start']) }}>
    @foreach ($steps as $step => $label)
        @if (!$loop->first)
            <div class="mt-4 h-0.5 flex-1 {{ $current > $loop->iteration - 1 ? 'bg-primary' : 'bg-app-border' }}"></div>
        @endif

        <div class="flex flex-col items-center {{ $loop->first || $loop->last ? '' : 'px-2' }}">
            <span @class([
                'flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-semibold',
                'bg-primary text-white' => $current >= $step,
                'bg-secondary-light text-secondary' => $current < $step,
            ])>
                @if ($current > $step)
                    <x-icon name="check" class="w-4 h-4" />
                @else
                    {{ $step }}
                @endif
            </span>
            <span @class([
                'mt-2 whitespace-nowrap text-xs font-medium',
                'text-primary' => $current === $step,
                'text-secondary-dark' => $current > $step,
                'text-secondary' => $current < $step,
            ])>{{ $label }}</span>
        </div>
    @endforeach
</div>
