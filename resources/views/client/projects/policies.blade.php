@php
    $brandLabel = $project->brand_name ?? $project->project_name;
@endphp

<x-client-layout title="Policies">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('client.projects.index') }}" class="hover:text-primary">My Projects</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <a href="{{ route('client.projects.show', $project) }}" class="hover:text-primary">{{ $brandLabel }}</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">Policies</span>
    </nav>

    <div class="mt-3 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div>
            <h1 class="text-xl font-semibold text-secondary-dark">Policies</h1>
            <p class="text-sm text-secondary">{{ $brandLabel }} ({{ $project->product->name }})</p>
        </div>

        <a href="{{ route('client.projects.show', $project) }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg border border-app-border bg-white px-4 py-2 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
            <x-icon name="chevron-left" class="w-4 h-4" />
            Back to Project
        </a>
    </div>

    <div class="mt-6">
        @include('client.projects.tabs.policies')
    </div>
</x-client-layout>
