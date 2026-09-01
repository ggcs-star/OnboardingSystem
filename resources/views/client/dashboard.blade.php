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

    $nextRenewal = ($renewals ?? collect())->first();
@endphp

<x-client-layout title="Dashboard">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Dashboard</h1>
        <p class="mt-1 text-sm text-secondary">Track your onboarding progress across every project</p>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-5">
        <a href="{{ route('client.projects.index') }}" class="block">
            <x-stat-card label="Total Projects" :value="$stats['total_projects']" :hint="$stats['active_projects'] . ' active'" tint="primary" icon="folder" />
        </a>
        <a href="{{ $projects->isNotEmpty() ? route('client.projects.documents', $projects->first()) : route('client.projects.index') }}" class="block">
            <x-stat-card label="Documents Submitted" :value="$stats['docs_done'] . '/' . $stats['docs_total']" hint="across all projects" tint="success" icon="file-text" />
        </a>
        <a href="{{ route('client.courses.index') }}" class="block">
            <x-stat-card label="Course Lessons" :value="$stats['course_lessons_done'] . '/' . $stats['course_lessons_total']" hint="watched across courses" tint="violet" icon="play-circle" />
        </a>
        <a href="{{ route('client.support.index') }}" class="block">
            <x-stat-card label="Open Tickets" :value="$stats['open_tickets']" hint="unresolved" tint="danger" icon="life-buoy" />
        </a>
        <a href="{{ $nextRenewal ? route('client.projects.show', $nextRenewal->project) : route('client.projects.index') }}" class="block">
            <x-stat-card label="Next Renewal" :value="$nextRenewal?->expiry_date?->format('d M Y') ?? 'None'" :hint="$nextRenewal ? ucfirst($nextRenewal->renewal_status) . ' · ' . $nextRenewal->plan_name : 'no active subscription'" tint="warning" icon="refresh-cw" />
        </a>
    </div>

    @if ($stats['docs_total'] > 0)
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-5">
            <div class="rounded-xl border border-app-border bg-white p-6 lg:col-span-2">
                <p class="mb-4 text-xs font-semibold uppercase tracking-wide text-secondary">Your Documents Status</p>
                <x-charts.donut :segments="$documentDonutSegments" center-label="fields" />
            </div>

            <div class="rounded-xl border border-app-border bg-white p-6 lg:col-span-3">
                <p class="mb-4 text-xs font-semibold uppercase tracking-wide text-secondary">Overall Progress</p>
                <x-charts.meter label="Documents Submitted" :done="$stats['docs_done']" :total="$stats['docs_total']" icon="file-text" />
            </div>
        </div>
    @endif

    <div class="mt-6 rounded-xl border border-app-border bg-white">
        <div class="flex items-center justify-between p-6 pb-0">
            <p class="text-sm font-semibold text-secondary-dark">Your Projects</p>
            <a href="{{ route('client.projects.index') }}" class="text-xs font-medium text-primary hover:underline">View all</a>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-app-border text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                        <th class="px-6 py-3">Project</th>
                        <th class="px-5 py-3">Product</th>
                        <th class="px-5 py-3">Stage</th>
                        <th class="px-5 py-3">Documents</th>
                        <th class="px-5 py-3">Salesperson</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-app-border">
                    @forelse ($projects->take(4) as $project)
                        @php
                            [$docsDone, $docsTotal] = $project->documentsProgress();
                            $docsPct = $docsTotal ? round($docsDone / $docsTotal * 100) : 0;
                        @endphp
                        <tr class="hover:bg-surface-alt/60">
                            <td class="px-6 py-3">
                                <a href="{{ route('client.projects.show', $project) }}" class="font-medium text-primary hover:underline">
                                    {{ $project->brand_name ?? $project->project_name }}
                                </a>
                            </td>
                            <td class="px-5 py-3 text-secondary">{{ $project->product->name }}</td>
                            <td class="px-5 py-3">
                                <x-project-timeline :stage="$project->current_stage" :status="$project->status" />
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-24 shrink-0 overflow-hidden rounded-full bg-primary-light">
                                        <div class="h-2 rounded-full {{ $docsDone === $docsTotal && $docsTotal > 0 ? 'bg-success' : 'bg-primary' }}" style="width: {{ max($docsPct, $docsTotal ? 2 : 0) }}%"></div>
                                    </div>
                                    <span class="tabular-nums text-xs text-secondary">{{ $docsDone }}/{{ $docsTotal }} · {{ $docsPct }}%</span>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                @if ($project->salesEmployee)
                                    <p class="font-medium text-secondary-dark">{{ $project->salesEmployee->name }}</p>
                                    <p class="text-xs text-secondary">
                                        {{ $project->salesEmployee->phone }}
                                        @if ($project->salesEmployee->email)
                                            · {{ $project->salesEmployee->email }}
                                        @endif
                                    </p>
                                @else
                                    <span class="text-xs text-secondary">Not attributed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-secondary">
                                You don't have any projects assigned yet. Your onboarding team will notify you once one is created.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="h-2"></div>
    </div>

    <div class="mt-6 rounded-xl border border-app-border bg-white p-6">
        <p class="text-sm font-semibold text-secondary-dark">Quick Links</p>
        <p class="mb-5 text-xs text-secondary">Jump straight to the action you need</p>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
            <a href="{{ route('client.projects.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="folder" class="w-4 h-4 text-primary" /> Manage Projects
            </a>
            <a href="{{ route('client.customization-requests.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="layers" class="w-4 h-4 text-primary" /> Add Customization Request
            </a>
            <a href="{{ route('client.support.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="life-buoy" class="w-4 h-4 text-primary" /> Raise Support Ticket
            </a>
            <a href="{{ route('client.courses.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="play-circle" class="w-4 h-4 text-primary" /> Browse Courses
            </a>
            <a href="{{ route('client.lms.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="book-open" class="w-4 h-4 text-primary" /> Documentation
            </a>
        </div>
    </div>
</x-client-layout>
