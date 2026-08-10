@php
    // Literal class names (not built via concatenation) so Tailwind's
    // content scanner can find and generate them.
    $chartColors = ['bg-chart-1', 'bg-chart-2', 'bg-chart-3', 'bg-chart-4', 'bg-chart-5'];
    $stageFunnelData = $stageCounts->values()->map(fn ($row, $i) => [
        'label' => $row['label'],
        'value' => $row['value'],
        'color' => $chartColors[$i] ?? 'bg-chart-1',
        'hint' => $row['hint'] ?? null,
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

    $customizationStatusBadge = [
        'pending' => 'bg-warning-light text-warning',
        'approved' => 'bg-success-light text-success',
        'rejected' => 'bg-danger-light text-danger',
        'partial' => 'bg-primary-light text-primary',
    ];
    $inquiryStatusBadge = [
        'pending' => 'bg-warning-light text-warning',
        'approved' => 'bg-success-light text-success',
        'rejected' => 'bg-danger-light text-danger',
    ];
    $lessonCompletionPct = $courseStats['lessons_started'] > 0
        ? round($courseStats['lessons_completed'] / $courseStats['lessons_started'] * 100)
        : 0;
@endphp

<x-admin-layout title="Dashboard">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">Dashboard</h1>
            <p class="mt-1 text-sm text-secondary">A complete overview of your products, clients and onboarding pipeline</p>
        </div>
        <p class="shrink-0 text-xs text-secondary">Last updated: {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-5">
        <x-stat-card label="Products" :value="$productStats['total']" :hint="$productStats['active'] . ' active'" tint="primary" icon="box" />
        <x-stat-card label="Clients" :value="$clientStats['total']" :hint="$clientStats['active'] . ' active · ' . $clientStats['blocked'] . ' inactive'" tint="teal" icon="users" />
        <x-stat-card label="Active Projects" :value="$projectStats['active_projects']" :hint="$projectStats['total_projects'] . ' total'" tint="success" icon="folder" />
        <x-stat-card label="Documents Pending" :value="$projectStats['documents_pending']" hint="projects need attention" tint="warning" icon="file-text" />
        <x-stat-card label="Open Tickets" :value="$projectStats['open_support_tickets']" hint="unresolved" tint="violet" icon="life-buoy" />
    </div>

    <div class="mt-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Revenue This Month" :value="'₹' . number_format($renewalStats['revenue_this_month'], 0)" :hint="'₹' . number_format($renewalStats['revenue_total'], 0) . ' all-time'" tint="teal" icon="refresh-cw" />
        <x-stat-card label="Pending Customizations" :value="$customizationCounts->get('pending', 0)" :hint="$customizationCounts->sum() . ' total requests'" tint="violet" icon="layers" />
        <x-stat-card label="Quiz Answers to Grade" :value="$pendingQuizGrading" hint="text answers awaiting review" tint="warning" icon="help-circle" />
        <x-stat-card label="New Inquiries" :value="$inquiryCounts->get('pending', 0)" :hint="$inquiryCounts->sum() . ' total inquiries'" tint="primary" icon="tag" />
    </div>

    <div class="mt-6 rounded-xl border border-app-border bg-white">
        <div class="flex flex-col gap-3 p-6 pb-0 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm font-semibold text-secondary-dark">Recent Projects</p>
            <a href="{{ route('admin.projects.index') }}" class="text-xs font-medium text-primary hover:underline">View all</a>
        </div>

        <form method="GET" action="{{ route('admin.dashboard') }}" class="mt-4 flex flex-col gap-3 px-6 sm:flex-row sm:flex-wrap sm:items-center">
            <input type="hidden" name="recent_range" value="{{ $recentFilters['range'] }}">
            <input type="hidden" name="inquiry_status" value="{{ $inquiryFilters['status'] }}">
            <input type="hidden" name="inquiry_client" value="{{ $inquiryFilters['client'] }}">
            <input type="hidden" name="inquiry_range" value="{{ $inquiryFilters['range'] }}">

            <select name="recent_product" onchange="this.form.submit()" class="rounded-lg border-app-border text-xs shadow-sm focus:border-primary focus:ring-primary">
                <option value="">All Products</option>
                @foreach ($allProducts as $product)
                    <option value="{{ $product->id }}" @selected((string) $recentFilters['product'] === (string) $product->id)>{{ $product->name }}</option>
                @endforeach
            </select>

            <select name="recent_stage" onchange="this.form.submit()" class="rounded-lg border-app-border text-xs shadow-sm focus:border-primary focus:ring-primary">
                <option value="">All Stages</option>
                @foreach (\App\Models\Project::STAGES as $key => $label)
                    <option value="{{ $key }}" @selected($recentFilters['stage'] === $key)>{{ $label }}</option>
                @endforeach
            </select>

            <div class="inline-flex rounded-lg border border-app-border p-0.5 text-xs font-medium">
                @foreach (['7d' => '7D', '30d' => '1M', '365d' => '1Y', 'all' => 'All'] as $value => $label)
                    <a
                        href="{{ request()->fullUrlWithQuery(['recent_range' => $value]) }}"
                        class="rounded-md px-3 py-1.5 {{ $recentFilters['range'] === $value ? 'bg-primary text-white' : 'text-secondary-dark hover:bg-surface-alt' }}"
                    >{{ $label }}</a>
                @endforeach
            </div>

            @if ($recentFilters['product'] || $recentFilters['stage'] || $recentFilters['range'] !== 'all')
                <a href="{{ request()->fullUrlWithQuery(['recent_product' => null, 'recent_stage' => null, 'recent_range' => null]) }}" class="text-xs font-medium text-secondary hover:text-danger">Clear</a>
            @endif
        </form>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-app-border text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                        <th class="px-6 py-3">Project</th>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Client</th>
                        <th class="px-6 py-3">Stage</th>
                        <th class="px-6 py-3">Docs</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-app-border">
                    @forelse ($recentProjects as $project)
                        @php
                            $stageBadge = project_stage_badge($project->current_stage, $project->status);
                            [$docsDone, $docsTotal] = $project->documentsProgress();
                        @endphp
                        <tr>
                            <td class="px-6 py-3 font-medium text-secondary-dark">
                                {{ $project->project_name }}
                            </td>
                            <td class="px-6 py-3 text-secondary">{{ $project->product->name }}</td>
                            <td class="px-6 py-3 text-secondary">{{ $project->client->company_name }}</td>
                            <td class="px-6 py-3">
                                <x-badge :classes="$stageBadge['classes']" dot>{{ $stageBadge['label'] }}</x-badge>
                            </td>
                            <td class="px-6 py-3 tabular-nums text-secondary">{{ $docsDone }}/{{ $docsTotal }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-secondary">No projects yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="h-6"></div>
    </div>

    <div class="mt-6 rounded-xl border border-app-border bg-white p-6">
        <p class="text-sm font-semibold text-secondary-dark">Projects by Product</p>
        <p class="mb-5 text-xs text-secondary">Which products have the most active onboarding work</p>
        @if ($productCounts->isEmpty())
            <p class="text-sm text-secondary">No projects yet.</p>
        @else
            <div class="max-h-80 overflow-y-auto pr-2">
                <x-charts.bar-list :data="$productCounts->map(fn ($row) => ['label' => $row['label'], 'value' => $row['value'], 'color' => 'bg-primary'])" />
            </div>
        @endif
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

    <div class="mt-8">
        <h2 class="text-xs font-bold uppercase tracking-wide text-secondary">Growth</h2>
    </div>

    <div class="mt-3 rounded-xl border border-app-border bg-white">
        <div class="flex flex-col gap-1 p-6 pb-0 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-secondary-dark">Product Inquiries</p>
                <p class="text-xs text-secondary">Overview of all products and their inquiry status</p>
            </div>
            <a href="{{ route('admin.product.inquiry.index') }}" class="shrink-0 text-xs font-medium text-primary hover:underline">View all</a>
        </div>

        <form method="GET" action="{{ route('admin.dashboard') }}" class="mt-4 flex flex-col gap-3 px-6 sm:flex-row sm:flex-wrap sm:items-center">
            <input type="hidden" name="inquiry_range" value="{{ $inquiryFilters['range'] }}">
            <input type="hidden" name="recent_product" value="{{ $recentFilters['product'] }}">
            <input type="hidden" name="recent_stage" value="{{ $recentFilters['stage'] }}">
            <input type="hidden" name="recent_range" value="{{ $recentFilters['range'] }}">

            <select name="inquiry_status" onchange="this.form.submit()" class="rounded-lg border-app-border text-xs shadow-sm focus:border-primary focus:ring-primary">
                <option value="">All Statuses</option>
                <option value="pending" @selected($inquiryFilters['status'] === 'pending')>Pending</option>
                <option value="approved" @selected($inquiryFilters['status'] === 'approved')>Approved</option>
                <option value="rejected" @selected($inquiryFilters['status'] === 'rejected')>Rejected</option>
            </select>

            <select name="inquiry_client" onchange="this.form.submit()" class="rounded-lg border-app-border text-xs shadow-sm focus:border-primary focus:ring-primary">
                <option value="">All Clients</option>
                @foreach ($inquiryClients as $client)
                    <option value="{{ $client->id }}" @selected((string) $inquiryFilters['client'] === (string) $client->id)>{{ $client->company_name }}</option>
                @endforeach
            </select>

            <div class="inline-flex rounded-lg border border-app-border p-0.5 text-xs font-medium">
                @foreach (['7d' => '7D', '30d' => '1M', '365d' => '1Y', 'all' => 'All'] as $value => $label)
                    <a
                        href="{{ request()->fullUrlWithQuery(['inquiry_range' => $value]) }}"
                        class="rounded-md px-3 py-1.5 {{ $inquiryFilters['range'] === $value ? 'bg-primary text-white' : 'text-secondary-dark hover:bg-surface-alt' }}"
                    >{{ $label }}</a>
                @endforeach
            </div>
        </form>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-app-border text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Contact Email</th>
                        <th class="px-6 py-3">Client</th>
                        <th class="px-6 py-3">Total Inquiries</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Trend</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-app-border">
                    @forelse ($inquiryTable as $row)
                        <tr>
                            <td class="px-6 py-3 font-medium text-secondary-dark">{{ $row['product']->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-3 text-secondary">{{ $row['email'] }}</td>
                            <td class="px-6 py-3 text-secondary">{{ $row['client']->company_name ?? '—' }}</td>
                            <td class="px-6 py-3 tabular-nums text-secondary">{{ $row['total'] }}</td>
                            <td class="px-6 py-3">
                                <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $inquiryStatusBadge[$row['status']] ?? 'bg-secondary-light text-secondary' }}">
                                    {{ ucfirst($row['status']) }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <x-charts.sparkline :data="$row['trend']" :color="match ($row['status']) {
                                    'approved' => 'rgb(var(--color-success))',
                                    'rejected' => 'rgb(var(--color-danger))',
                                    default => 'rgb(var(--color-warning))',
                                }" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-secondary">No inquiries match these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="px-6 py-4 text-xs text-secondary">Showing {{ $inquiryTable->count() }} of {{ $inquiryProductsTotal }} product{{ $inquiryProductsTotal === 1 ? '' : 's' }}.</p>
    </div>

    <div class="mt-8">
        <h2 class="text-xs font-bold uppercase tracking-wide text-secondary">Delivery</h2>
    </div>

    <div class="mt-3 grid grid-cols-1 gap-6 xl:grid-cols-5">
        <div class="rounded-xl border border-app-border bg-white p-6 xl:col-span-3">
            <p class="text-sm font-semibold text-secondary-dark">Customization Requests</p>
            <p class="mb-5 text-xs text-secondary">Latest pending requests awaiting review</p>
            @if ($pendingCustomizations->isEmpty())
                <p class="text-sm text-secondary">No pending customization requests.</p>
            @else
                <div class="divide-y divide-app-border">
                    @foreach ($pendingCustomizations as $request)
                        <div class="flex items-center justify-between gap-3 py-2.5 first:pt-0 last:pb-0">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-secondary-dark">{{ $request->title }}</p>
                                <p class="truncate text-xs text-secondary">{{ $request->project->client->company_name }} · {{ $request->project->brand_name ?? $request->project->project_name }}</p>
                            </div>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $customizationStatusBadge[$request->status] ?? 'bg-secondary-light text-secondary' }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-xl border border-app-border bg-white p-6 xl:col-span-2">
            <p class="text-sm font-semibold text-secondary-dark">Sales Performance</p>
            <p class="mb-5 text-xs text-secondary">Active sales employees by projects brought in</p>
            @if ($topSalesEmployees->isEmpty())
                <p class="text-sm text-secondary">No sales activity yet.</p>
            @else
                <x-charts.bar-list :data="$topSalesEmployees" />
            @endif
        </div>
    </div>

    <div class="mt-8">
        <h2 class="text-xs font-bold uppercase tracking-wide text-secondary">Learning</h2>
    </div>

    <div class="mt-3 rounded-xl border border-app-border bg-white p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-secondary-dark">LMS & Courses</p>
                <p class="text-xs text-secondary">Help-center and course adoption</p>
            </div>
            @if ($pendingQuizGrading > 0)
                <a href="{{ route('admin.course-quiz-answers.pending') }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-warning-light px-3 py-2 text-xs font-semibold text-warning hover:bg-warning/20">
                    <x-icon name="help-circle" class="w-4 h-4" />
                    {{ $pendingQuizGrading }} answer{{ $pendingQuizGrading === 1 ? '' : 's' }} awaiting grading
                </a>
            @endif
        </div>

        <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <x-stat-card label="LMS Products" :value="$lmsStats['products']" tint="primary" icon="book-open" compact />
            <x-stat-card label="Articles" :value="$lmsStats['articles']" :hint="$lmsStats['published_articles'] . ' published'" tint="teal" icon="file-text" compact />
            <x-stat-card label="Courses" :value="$courseStats['total']" :hint="$courseStats['published'] . ' published'" tint="violet" icon="play-circle" compact />
            <x-stat-card label="Lessons Completed" :value="$lessonCompletionPct . '%'" :hint="$courseStats['lessons_completed'] . '/' . $courseStats['lessons_started'] . ' started lessons'" tint="success" icon="check" compact />
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
            <a href="{{ route('admin.documents.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="file-text" class="w-4 h-4 text-primary" /> Documents
            </a>
            <a href="{{ route('admin.sales-employees.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="briefcase" class="w-4 h-4 text-primary" /> Sales Employees
            </a>
            <a href="{{ route('admin.customization-requests.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="layers" class="w-4 h-4 text-primary" /> Customization Requests
            </a>
            <a href="{{ route('admin.support.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="life-buoy" class="w-4 h-4 text-primary" /> Support
            </a>
            <a href="{{ route('admin.lms.products.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="book-open" class="w-4 h-4 text-primary" /> LMS
            </a>
            <a href="{{ route('admin.courses.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="play-circle" class="w-4 h-4 text-primary" /> Courses
            </a>
            <a href="{{ route('admin.product.inquiry.index') }}" class="flex items-center gap-2 rounded-lg border border-app-border px-3 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="tag" class="w-4 h-4 text-primary" /> Product Inquiries
            </a>
        </div>
    </div>
</x-admin-layout>
