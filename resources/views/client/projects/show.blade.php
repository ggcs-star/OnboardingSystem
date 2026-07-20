@php
    $stageBadge = project_stage_badge($project->current_stage, $project->status);
@endphp

<x-client-layout :title="$project->project_name">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('client.dashboard') }}" class="hover:text-primary">My Projects</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">{{ $project->project_name }}</span>
    </nav>

    <div class="mt-3 flex items-center gap-2">
        <h1 class="text-xl font-semibold text-secondary-dark">{{ $project->project_name }}</h1>
        <x-badge :classes="$stageBadge['classes']" dot>{{ $stageBadge['label'] }}</x-badge>
    </div>
    <p class="text-sm text-secondary">{{ $project->product->name }}</p>

    <x-client-progress-timeline
        class="mt-6"
        :project="$project"
        heading="Your Progress"
        :footer="'Your project is currently in ' . $stageBadge['label'] . ' — we\'ll notify you as it moves forward.'"
    />

    <div class="mt-6 border-b border-app-border">
        <nav class="-mb-px flex gap-6 overflow-x-auto">
            @foreach ($tabs as $key => $tab)
                <x-tab-link :href="route('client.projects.show', ['project' => $project, 'tab' => $key])" :active="$activeTab === $key" :icon="$tab['icon']">
                    {{ $tab['label'] }}
                </x-tab-link>
            @endforeach
        </nav>
    </div>

    <div class="mt-6">
        @include('client.projects.tabs.' . $activeTab)
    </div>
</x-client-layout>
