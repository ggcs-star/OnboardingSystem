@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'w-full border-app-border focus:border-primary focus:ring-primary rounded-lg shadow-sm text-sm']) !!}>
