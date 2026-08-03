@php
    $stageBadge = project_stage_badge($project->current_stage, $project->status);
    $entries = $project->documentEntries();
    $statusCounts = $entries->countBy('status');
    [$docsDone, $docsTotal] = $project->documentsProgress();
    [$videosDone, $videosTotal] = $project->trainingProgressCount();
    $overallTotal = $docsTotal + $videosTotal;
    $overallPct = $overallTotal > 0 ? (int) round((($docsDone + $videosDone) / $overallTotal) * 100) : 0;
    $lastActivity = $project->updated_at->isToday()
        ? 'Today, ' . $project->updated_at->format('g:i A')
        : $project->updated_at->format('d M, g:i A');
@endphp

<x-client-layout :title="$project->product->name">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('client.dashboard') }}" class="hover:text-primary">My Projects</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">{{ $project->product->name }}</span>
    </nav>

    <div class="mt-3 flex items-center gap-2">
        <h1 class="text-xl font-semibold text-secondary-dark">{{ $project->product->name }}</h1>
        <x-badge :classes="$stageBadge['classes']" dot>{{ $stageBadge['label'] }}</x-badge>
        @if ($readOnly)
            <x-badge classes="bg-secondary-light text-secondary-dark">View Only</x-badge>
        @endif
    </div>
    <p class="text-sm text-secondary">{{ $project->brand_name ?? $project->project_name }}</p>

    <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        <div class="rounded-xl border border-app-border bg-white p-5">
            <p class="text-xs font-medium uppercase tracking-wide text-secondary">Your Progress</p>
            <p class="mt-2 text-3xl font-semibold text-secondary-dark">{{ $overallPct }}%</p>
            <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-primary-light">
                <div class="h-2 rounded-full {{ $overallPct >= 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ $overallPct }}%"></div>
            </div>
            <p class="mt-2 text-xs text-secondary">Last updated: {{ $lastActivity }}</p>
        </div>
        <x-stat-card label="Documents" value="{{ $docsDone }}/{{ $docsTotal }}" :badge="($docsTotal ? round($docsDone / $docsTotal * 100) : 0) . '%'" icon="file-text" />
        <x-stat-card label="Training Videos" value="{{ $videosDone }}/{{ $videosTotal }}" :badge="($videosTotal ? round($videosDone / $videosTotal * 100) : 0) . '%'" icon="play-circle" />
        <x-stat-card label="Pending" :value="$statusCounts->get('pending', 0)" icon="clock" tint="warning" />
        <x-stat-card label="Approved" :value="$statusCounts->get('approved', 0)" icon="check" tint="success" />
    </div>

    <div class="mt-6 rounded-xl border border-app-border bg-white p-6">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary">Project Pipeline</h2>
        <div class="mt-5 overflow-x-auto pb-1">
            <x-project-timeline :stage="$project->current_stage" :status="$project->status" show-labels />
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-3">
        <div class="space-y-8 lg:col-span-2">
            <div id="documents">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-secondary">Documents</h2>
                @include('client.projects.tabs.documents', ['readOnly' => $readOnly])
            </div>
        </div>

        <div class="space-y-8">
            <div id="sales-contact">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-secondary">Sales Contact</h2>
                @include('client.projects.tabs.sales-contact')
            </div>

            <div id="renewal">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-secondary">Subscription</h2>
                @include('client.projects.tabs.renewal')
            </div>
        </div>
    </div>
</x-client-layout>
