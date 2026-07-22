@php
    $statusVars = [
        'pending' => '--color-warning',
        'submitted' => '--color-primary',
        'approved' => '--color-success',
        'rejected' => '--color-danger',
    ];
    $statusSwatchClasses = [
        'pending' => 'bg-warning',
        'submitted' => 'bg-primary',
        'approved' => 'bg-success',
        'rejected' => 'bg-danger',
    ];
    $documentDonutSegments = ($documentStatusCounts ?? collect())->map(fn ($row) => [
        'label' => ucfirst($row['status']),
        'count' => $row['count'],
        'var' => $statusVars[$row['status']] ?? '--color-secondary',
        'swatchClass' => $statusSwatchClasses[$row['status']] ?? 'bg-secondary',
    ]);
@endphp

<x-client-layout title="Dashboard">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Dashboard</h1>
        <p class="mt-1 text-sm text-secondary">Track your onboarding progress across every project</p>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Total Projects" :value="$stats['total_projects']" :hint="$stats['active_projects'] . ' active'" tint="primary" icon="folder" />
        <x-stat-card label="Documents Submitted" :value="$stats['docs_done'] . '/' . $stats['docs_total']" hint="across all projects" tint="success" icon="file-text" />
        <x-stat-card label="Training Watched" :value="$stats['training_done'] . '/' . $stats['training_total']" hint="across all projects" tint="teal" icon="video" />
        <x-stat-card label="Open Tickets" :value="$stats['open_tickets']" hint="unresolved" tint="danger" icon="life-buoy" />
    </div>

    @if ($stats['docs_total'] > 0 || $stats['training_total'] > 0)
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-5">
            <div class="rounded-xl border border-app-border bg-white p-6 lg:col-span-2">
                <p class="mb-4 text-xs font-semibold uppercase tracking-wide text-secondary">Your Documents Status</p>
                <x-charts.donut :segments="$documentDonutSegments" center-label="fields" />
            </div>

            <div class="rounded-xl border border-app-border bg-white p-6 lg:col-span-3">
                <p class="mb-4 text-xs font-semibold uppercase tracking-wide text-secondary">Overall Progress</p>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-charts.meter label="Documents Submitted" :done="$stats['docs_done']" :total="$stats['docs_total']" icon="file-text" />
                    <x-charts.meter label="Training Watched" :done="$stats['training_done']" :total="$stats['training_total']" icon="video" />
                </div>
            </div>
        </div>
    @endif

    <div class="mt-6 rounded-xl border border-app-border bg-white">
        <div class="flex items-center justify-between p-6 pb-0">
            <p class="text-sm font-semibold text-secondary-dark">Your Projects</p>
            <a href="{{ route('client.projects.index') }}" class="text-xs font-medium text-primary hover:underline">View all</a>
        </div>

        <div class="mt-4">
            @forelse ($projects->take(4) as $project)
                @php
                    $stageBadge = project_stage_badge($project->current_stage, $project->status);
                    [$docsDone, $docsTotal] = $project->documentsProgress();
                    [$trainingDone, $trainingTotal] = $project->trainingProgressCount();
                @endphp
                <a href="{{ route('client.projects.show', $project) }}" class="flex items-center justify-between gap-4 border-t border-app-border px-6 py-4 hover:bg-surface-alt">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                            <x-icon name="box" class="w-4 h-4" />
                        </span>
                        <div>
                            <p class="text-sm font-medium text-secondary-dark">{{ $project->product->name }}</p>
                            <p class="text-xs text-secondary">{{ $project->brand_name ?? $project->project_name }}</p>
                        </div>
                    </div>
                    <div class="hidden text-xs text-secondary sm:block">{{ $docsDone }}/{{ $docsTotal }} docs · {{ $trainingDone }}/{{ $trainingTotal }} videos</div>
                    <x-badge :classes="$stageBadge['classes']" dot>{{ $stageBadge['label'] }}</x-badge>
                </a>
            @empty
                <p class="border-t border-app-border px-6 py-8 text-center text-sm text-secondary">
                    You don't have any projects assigned yet. Your onboarding team will notify you once one is created.
                </p>
            @endforelse
        </div>
        <div class="h-2"></div>
    </div>
</x-client-layout>
