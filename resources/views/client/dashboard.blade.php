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

    $renewalStatusBadge = [
        'active' => 'bg-success-light text-success',
        'expiring' => 'bg-warning-light text-warning',
        'expired' => 'bg-danger-light text-danger',
        'none' => 'bg-secondary-light text-secondary',
    ];
    $customizationStatusBadge = [
        'pending' => 'bg-warning-light text-warning',
        'approved' => 'bg-success-light text-success',
        'rejected' => 'bg-danger-light text-danger',
        'partial' => 'bg-primary-light text-primary',
    ];
    $nextRenewal = ($renewals ?? collect())->first();
@endphp

<x-client-layout title="Dashboard">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Dashboard</h1>
        <p class="mt-1 text-sm text-secondary">Track your onboarding progress across every project</p>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-5">
        <x-stat-card label="Total Projects" :value="$stats['total_projects']" :hint="$stats['active_projects'] . ' active'" tint="primary" icon="folder" />
        <x-stat-card label="Documents Submitted" :value="$stats['docs_done'] . '/' . $stats['docs_total']" hint="across all projects" tint="success" icon="file-text" />
        <x-stat-card label="Course Lessons" :value="$stats['course_lessons_done'] . '/' . $stats['course_lessons_total']" hint="watched across courses" tint="violet" icon="play-circle" />
        <x-stat-card label="Open Tickets" :value="$stats['open_tickets']" hint="unresolved" tint="danger" icon="life-buoy" />
        <x-stat-card label="Next Renewal" :value="$nextRenewal?->expiry_date?->format('d M Y') ?? 'None'" :hint="$nextRenewal ? ucfirst($nextRenewal->renewal_status) . ' · ' . $nextRenewal->plan_name : 'no active subscription'" tint="warning" icon="refresh-cw" />
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

        <div class="mt-4">
            @forelse ($projects->take(4) as $project)
                @php
                    $stageBadge = project_stage_badge($project->current_stage, $project->status);
                    [$docsDone, $docsTotal] = $project->documentsProgress();
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
                    <div class="hidden text-xs text-secondary sm:block">{{ $docsDone }}/{{ $docsTotal }} docs</div>
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

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-5">
        <div class="rounded-xl border border-app-border bg-white p-6 lg:col-span-2">
            <p class="text-sm font-semibold text-secondary-dark">Your Subscriptions</p>
            <p class="mb-5 text-xs text-secondary">Plan and renewal status per project</p>
            @if ($renewals->isEmpty())
                <p class="text-sm text-secondary">No active subscriptions yet.</p>
            @else
                <div class="divide-y divide-app-border">
                    @foreach ($renewals as $renewal)
                        <div class="flex items-center justify-between gap-3 py-2.5 first:pt-0 last:pb-0">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-secondary-dark">{{ $renewal->project->brand_name ?? $renewal->project->project_name }}</p>
                                <p class="truncate text-xs text-secondary">{{ $renewal->plan_name }} · expires {{ $renewal->expiry_date?->format('d M Y') }}</p>
                            </div>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $renewalStatusBadge[$renewal->renewal_status] ?? 'bg-secondary-light text-secondary' }}">
                                {{ ucfirst($renewal->renewal_status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-xl border border-app-border bg-white p-6 lg:col-span-3">
            <p class="text-sm font-semibold text-secondary-dark">Course Progress</p>
            <p class="mb-5 text-xs text-secondary">% of lessons watched per course</p>
            @if ($courseProgress->isEmpty())
                <p class="text-sm text-secondary">No courses assigned yet.</p>
            @else
                <x-charts.bar-list :data="$courseProgress" unit="%" />
            @endif
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-app-border bg-white">
        <div class="flex items-center justify-between p-6 pb-0">
            <p class="text-sm font-semibold text-secondary-dark">Customization Requests</p>
            <a href="{{ route('client.customization-requests.index') }}" class="text-xs font-medium text-primary hover:underline">View all</a>
        </div>

        <div class="mt-4">
            @forelse ($recentCustomizationRequests as $request)
                <div class="flex items-center justify-between gap-3 border-t border-app-border px-6 py-3">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-secondary-dark">{{ $request->title }}</p>
                        <p class="truncate text-xs text-secondary">{{ $request->project->brand_name ?? $request->project->project_name }}</p>
                    </div>
                    <span class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $customizationStatusBadge[$request->status] ?? 'bg-secondary-light text-secondary' }}">
                        {{ ucfirst($request->status) }}
                    </span>
                </div>
            @empty
                <p class="border-t border-app-border px-6 py-8 text-center text-sm text-secondary">
                    You haven't submitted any customization requests yet.
                </p>
            @endforelse
        </div>
        <div class="h-2"></div>
    </div>

    <div class="mt-6 rounded-xl border border-app-border bg-white p-6">
        <p class="text-sm font-semibold text-secondary-dark">Quick Links</p>
        <p class="mb-5 text-xs text-secondary">Jump straight to the section you need</p>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <a href="{{ route('client.courses.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="play-circle" class="w-4 h-4 text-primary" /> Courses
            </a>
            <a href="{{ route('client.lms.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="book-open" class="w-4 h-4 text-primary" /> Documentation
            </a>
            <a href="{{ route('client.support.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="life-buoy" class="w-4 h-4 text-primary" /> Support
            </a>
            <a href="{{ route('client.customization-requests.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="layers" class="w-4 h-4 text-primary" /> Customizations
            </a>
        </div>
    </div>
</x-client-layout>
