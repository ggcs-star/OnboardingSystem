@php
    // Literal class names (not built via concatenation) so Tailwind's
    // content scanner can find and generate them.
    $chartColors = ['bg-chart-1', 'bg-chart-2', 'bg-chart-3', 'bg-chart-4', 'bg-chart-5'];
    $stageFunnelData = $stageCounts->values()->map(fn ($row, $i) => [
        'label' => $row['label'],
        'value' => $row['value'],
        'color' => $chartColors[$i] ?? 'bg-chart-1',
    ]);

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
    $documentDonutSegments = $documentStatusCounts->map(fn ($row) => [
        'label' => ucfirst($row['status']),
        'count' => $row['count'],
        'var' => $statusVars[$row['status']] ?? '--color-secondary',
        'swatchClass' => $statusSwatchClasses[$row['status']] ?? 'bg-secondary',
    ]);
@endphp

<x-admin-layout title="Dashboard">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">Dashboard</h1>
            <p class="mt-1 text-sm text-secondary">A complete overview of your products, clients and onboarding pipeline</p>
        </div>
        <p class="shrink-0 text-xs text-secondary">Last updated: {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-6">
        <x-stat-card label="Products" :value="$productStats['total']" :hint="$productStats['active'] . ' active'" tint="primary" icon="box" />
        <x-stat-card label="Clients" :value="$clientStats['total']" :hint="$clientStats['active'] . ' active · ' . $clientStats['blocked'] . ' blocked'" tint="teal" icon="users" />
        <x-stat-card label="Active Projects" :value="$projectStats['active_projects']" :hint="$projectStats['total_projects'] . ' total'" tint="success" icon="folder" />
        <x-stat-card label="Documents Pending" :value="$projectStats['documents_pending']" hint="projects need attention" tint="warning" icon="file-text" />
        <x-stat-card label="Open Tickets" :value="$projectStats['open_support_tickets']" hint="unresolved" tint="violet" icon="life-buoy" />
        <x-stat-card label="Renewals Alert" :value="$projectStats['renewals_alert']" hint="expiring within 30 days" tint="danger" icon="refresh-cw" />
    </div>

    <div class="mt-6 rounded-xl border border-app-border bg-white">
        <div class="flex items-center justify-between p-6 pb-0">
            <p class="text-sm font-semibold text-secondary-dark">Recent Projects</p>
            <a href="{{ route('admin.projects.index') }}" class="text-xs font-medium text-primary hover:underline">View all</a>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-app-border text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                        <th class="px-6 py-3">Project</th>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Client</th>
                        <th class="px-6 py-3">Stage</th>
                        <th class="px-6 py-3">Docs</th>
                        <th class="px-6 py-3">Training</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-app-border">
                    @forelse ($recentProjects as $project)
                        @php
                            $stageBadge = project_stage_badge($project->current_stage, $project->status);
                            [$docsDone, $docsTotal] = $project->documentsProgress();
                            [$trainingDone, $trainingTotal] = $project->trainingProgressCount();
                        @endphp
                        <tr>
                            <td class="px-6 py-3">
                                <a href="{{ route('admin.projects.show', $project) }}" class="font-medium text-secondary-dark hover:text-primary">
                                    {{ $project->project_name }}
                                </a>
                            </td>
                            <td class="px-6 py-3 text-secondary">{{ $project->product->name }}</td>
                            <td class="px-6 py-3 text-secondary">{{ $project->client->company_name }}</td>
                            <td class="px-6 py-3">
                                <x-badge :classes="$stageBadge['classes']" dot>{{ $stageBadge['label'] }}</x-badge>
                            </td>
                            <td class="px-6 py-3 tabular-nums text-secondary">{{ $docsDone }}/{{ $docsTotal }}</td>
                            <td class="px-6 py-3 tabular-nums text-secondary">{{ $trainingDone }}/{{ $trainingTotal }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-secondary">No projects yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="h-6"></div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-5">
        <div class="rounded-xl border border-app-border bg-white p-6 xl:col-span-3">
            <p class="text-sm font-semibold text-secondary-dark">Training Completion by Client</p>
            <p class="mb-5 text-xs text-secondary">% of training videos watched so far — lowest first, so you know who to follow up with</p>
            @if ($trainingByClient->isEmpty())
                <p class="text-sm text-secondary">No training videos assigned yet.</p>
            @else
                <x-charts.bar-list :data="$trainingByClient" unit="%" />
            @endif
        </div>

        <div class="rounded-xl border border-app-border bg-white p-6 xl:col-span-2">
            <p class="text-sm font-semibold text-secondary-dark">Projects by Product</p>
            <p class="mb-5 text-xs text-secondary">Which products have the most active onboarding work</p>
            @if ($productCounts->isEmpty())
                <p class="text-sm text-secondary">No projects yet.</p>
            @else
                <x-charts.bar-list :data="$productCounts->map(fn ($row) => ['label' => $row['label'], 'value' => $row['value'], 'color' => 'bg-primary'])" />
            @endif
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-app-border bg-white p-6">
        <p class="text-sm font-semibold text-secondary-dark">Quick Links</p>
        <p class="mb-5 text-xs text-secondary">Jump straight to the section you need</p>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <a href="{{ route('admin.products.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="box" class="w-4 h-4 text-primary" /> Products
            </a>
            <a href="{{ route('admin.clients.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="users" class="w-4 h-4 text-primary" /> Clients
            </a>
            <a href="{{ route('admin.projects.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="folder" class="w-4 h-4 text-primary" /> Projects
            </a>
            <a href="{{ route('admin.documents.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="file-text" class="w-4 h-4 text-primary" /> Documents
            </a>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-5">
        <div class="rounded-xl border border-app-border bg-white p-6 xl:col-span-3">
            <p class="text-sm font-semibold text-secondary-dark">Project Pipeline</p>
            <p class="mb-5 text-xs text-secondary">How many projects currently sit at each stage of onboarding</p>
            <x-charts.funnel :data="$stageFunnelData" />
        </div>

        <div class="rounded-xl border border-app-border bg-white p-6 xl:col-span-2">
            <p class="text-sm font-semibold text-secondary-dark">Document Review Status</p>
            <p class="mb-5 text-xs text-secondary">Every document field submitted across all projects</p>
            <x-charts.donut :segments="$documentDonutSegments" center-label="fields" />
        </div>
    </div>
</x-admin-layout>
