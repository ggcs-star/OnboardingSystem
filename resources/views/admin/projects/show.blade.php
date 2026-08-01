@php
    $stageBadge = project_stage_badge($project->current_stage, $project->status);
    $statusBadge = project_status_badge($project->status);
    $lastActivity = $project->updated_at->isToday()
        ? 'Today, ' . $project->updated_at->format('g:i A')
        : $project->updated_at->format('d M, g:i A');
@endphp

<x-admin-layout :title="$project->brand_name ?? $project->project_name">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.projects.index') }}" class="hover:text-primary">Ongoing Projects</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">{{ $project->brand_name ?? $project->project_name }}</span>
    </nav>

    <div class="mt-3 flex flex-col justify-between gap-4 lg:flex-row lg:items-start">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                <x-icon name="box" class="w-5 h-5" />
            </span>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-semibold text-secondary-dark">{{ $project->brand_name ?? $project->project_name }}</h1>
                    <x-badge :classes="$statusBadge['classes']" dot>{{ $statusBadge['label'] }}</x-badge>
                </div>
                <p class="text-sm text-secondary">
                    {{ $project->product->category ?? $project->product->name }} ·
                    <a href="{{ route('admin.clients.show', $project->client) }}" class="hover:text-primary">{{ $project->client->company_name }}</a>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-secondary">
            <span class="flex items-center gap-1.5">
                <x-icon name="calendar" class="w-4 h-4 text-secondary/60" />
                Onboarded {{ $project->created_at->format('d M Y') }}
            </span>
            <span class="flex items-center gap-1.5">
                <x-icon name="users" class="w-4 h-4 text-secondary/60" />
                Client {{ $project->contactName() }}
            </span>
            <span class="flex items-center gap-1.5">
                <x-icon name="phone" class="w-4 h-4 text-secondary/60" />
                {{ $project->contactPhone() ?: 'No contact number' }}
            </span>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-6 gap-3">
        <div class="rounded-xl border border-app-border bg-white p-3">
            <p class="text-xs font-medium uppercase tracking-wide text-secondary">Overall Completion</p>
            <p class="mt-2 text-xl font-semibold text-secondary-dark">{{ $overallPct }}%</p>
            <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-primary-light">
                <div class="h-2 rounded-full {{ $overallPct >= 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ $overallPct }}%"></div>
            </div>
            <p class="mt-2 text-xs text-secondary">Last updated: {{ $lastActivity }}</p>
        </div>

        <x-stat-card compact label="Documents" value="{{ $docsDone }}/{{ $docsTotal }}" :badge="($docsTotal ? round($docsDone / $docsTotal * 100) : 0) . '%'" />
        <x-stat-card compact label="Training Videos" value="{{ $videosDone }}/{{ $videosTotal }}" :badge="($videosTotal ? round($videosDone / $videosTotal * 100) : 0) . '%'" />
        <x-stat-card compact label="Pending" :value="$pendingCount" icon="clock" tint="warning" />
        <x-stat-card compact label="Approved" :value="$approvedCount" icon="check" tint="success" />
        <x-stat-card compact label="Rejected" :value="$rejectedCount" icon="x" tint="danger" />
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-app-border bg-white p-6">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary">Pipeline Stage</h2>
                <div class="mt-5 overflow-x-auto pb-1">
                    <x-project-timeline :stage="$project->current_stage" :status="$project->status" show-labels />
                </div>
            </div>

            <div class="rounded-xl border border-app-border bg-white p-6">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary">Products &amp; Documents</h2>
                    <a href="{{ route('admin.projects.documents.index', ['project' => $project, 'readonly' => 1]) }}"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-app-border px-3 py-1.5 text-xs font-medium text-secondary-dark hover:bg-surface-alt">
                        <x-icon name="file-text" class="w-3.5 h-3.5" />
                        View All Documents
                    </a>
                </div>

                <div class="mt-4 rounded-lg border border-app-border" x-data="{ openProduct: true }">
                    <button type="button" x-on:click="openProduct = !openProduct"
                        class="flex w-full items-center justify-between gap-4 rounded-t-lg bg-surface-alt px-4 py-3 text-left">
                        <div class="flex items-center gap-2">
                            <x-icon name="box" class="w-4 h-4 text-secondary" />
                            <span class="font-medium text-secondary-dark">{{ $project->product->name }}</span>
                            <span class="text-xs text-secondary">(1 Brand)</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-secondary">{{ $docsDone }} Documents · {{ $pendingCount }} Pending</span>
                            <span class="text-xs font-semibold text-secondary-dark">{{ $docsTotal ? round($docsDone / $docsTotal * 100) : 0 }}%</span>
                            <x-icon name="chevron-down" class="w-4 h-4 shrink-0 text-secondary transition-transform" x-bind:class="openProduct && 'rotate-180'" />
                        </div>
                    </button>

                    <div x-show="openProduct" x-cloak class="border-t border-app-border" x-data="{ openBrand: true }">
                        <button type="button" x-on:click="openBrand = !openBrand"
                            class="flex w-full items-center justify-between gap-3 bg-surface-alt/40 px-4 py-2.5 text-left">
                            <span class="text-sm font-medium text-secondary-dark">{{ $project->brand_name ?? $project->project_name }}</span>
                            <x-icon name="chevron-down" class="w-3.5 h-3.5 shrink-0 text-secondary transition-transform" x-bind:class="openBrand && 'rotate-180'" />
                        </button>

                        <div x-show="openBrand" x-cloak class="grid grid-cols-1 gap-4 border-t border-app-border p-4 sm:grid-cols-2 xl:grid-cols-3">
                            @forelse ($groups as $group)
                                <x-projects.document-group-card :project="$project" :group="$group" />
                            @empty
                                <p class="text-sm text-secondary">No document groups defined for this product yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-app-border bg-white p-6">
                <h2 class="text-xs font-semibold uppercase tracking-wide text-secondary">Project Summary</h2>

                <div class="mt-4">
                    <p class="text-xs uppercase tracking-wide text-secondary">Client Contact</p>
                    <p class="mt-1 text-sm font-medium text-secondary-dark">{{ $project->contactName() }}</p>
                    <p class="text-sm text-secondary">{{ $project->contactPhone() ?: 'No contact number on file' }}</p>
                </div>

                <div class="mt-5 border-t border-app-border pt-4" x-data="{ editing: false }">
                    <div class="flex items-center justify-between">
                        <p class="text-xs uppercase tracking-wide text-secondary">Sold By</p>
                        <button type="button" x-on:click="editing = !editing" class="text-secondary hover:text-primary">
                            <x-icon name="edit" class="w-3.5 h-3.5" />
                        </button>
                    </div>

                    <div x-show="!editing">
                        <p class="mt-1 text-sm font-medium text-secondary-dark">{{ $project->salesEmployee->name ?? 'Not attributed' }}</p>
                        @if ($project->salesEmployee)
                            <p class="text-sm text-secondary">{{ $project->salesEmployee->email }}</p>
                        @endif
                    </div>

                    <form x-show="editing" x-cloak method="POST" action="{{ route('admin.projects.sales-employee.update', $project) }}" class="mt-2">
                        @csrf
                        @method('PATCH')
                        <select name="sales_employee_id" onchange="this.form.submit()"
                            class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                            <option value="">Not attributed</option>
                            @foreach ($salesEmployees as $salesEmployee)
                                <option value="{{ $salesEmployee->id }}" @selected($project->sales_employee_id === $salesEmployee->id)>
                                    {{ $salesEmployee->name }} · {{ $salesEmployee->phone }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="mt-5 border-t border-app-border pt-4" x-data="{ detailsOpen: false }">
                    <p class="text-xs uppercase tracking-wide text-secondary">Subscription Plan</p>

                    @if (! $project->renewal || ! $project->renewal->product_subscription_plan_id)
                        <p class="mt-2 text-sm text-secondary">No subscription plan chosen yet.</p>
                    @else
                        @php
                            $renewal = $project->renewal;
                            $renewalBadge = renewal_status_badge($renewalStatus);
                            $paid = $renewal->amountPaid();
                        @endphp
                        <div class="mt-1 flex items-center justify-between">
                            <p class="text-sm font-medium text-secondary-dark">{{ $renewal->plan_name }}</p>
                            @if ($renewal->go_live_date)
                                <x-badge :classes="$renewalBadge['classes']" dot>{{ $renewalBadge['label'] }}</x-badge>
                            @else
                                <x-badge classes="bg-warning-light text-warning" dot>Not yet activated</x-badge>
                            @endif
                        </div>
                        @if ($renewal->expiry_date)
                            <p class="text-xs text-secondary">Valid till {{ $renewal->expiry_date->format('d M Y') }}</p>
                        @endif

                        <button type="button" x-on:click="detailsOpen = !detailsOpen"
                            class="mt-2 text-xs font-medium text-primary hover:underline">
                            <span x-show="!detailsOpen">View Details</span>
                            <span x-show="detailsOpen" x-cloak>Hide Details</span>
                        </button>

                        <div x-show="detailsOpen" x-cloak class="mt-3">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-secondary">Amount</p>
                                    <p class="mt-1 font-medium text-secondary-dark">₹{{ number_format($renewal->renewal_amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-secondary">Paid</p>
                                    <p class="mt-1 font-medium text-secondary-dark">₹{{ number_format($paid, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-secondary">Start Date</p>
                                    <p class="mt-1 font-medium text-secondary-dark">{{ $renewal->go_live_date?->format('d-M-Y') ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-secondary">Expiry</p>
                                    <p class="mt-1 font-medium text-secondary-dark">{{ $renewal->expiry_date?->format('d-M-Y') ?? '—' }}</p>
                                </div>
                            </div>

                            @if ($renewal->history->isNotEmpty())
                                <div class="mt-4 overflow-x-auto rounded-lg border border-app-border">
                                    <table class="min-w-full divide-y divide-app-border text-sm">
                                        <thead>
                                            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                                                <th class="px-3 py-2">Date</th>
                                                <th class="px-3 py-2">Amount</th>
                                                <th class="px-3 py-2">Mode</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-app-border">
                                            @foreach ($renewal->history as $payment)
                                                <tr>
                                                    <td class="px-3 py-2 text-secondary">{{ $payment->payment_date->format('d-M-Y') }}</td>
                                                    <td class="px-3 py-2 font-medium text-secondary-dark">₹{{ number_format($payment->amount, 2) }}</td>
                                                    <td class="px-3 py-2 text-secondary">{{ $payment->payment_mode ?: '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-xl border border-app-border bg-white p-6">
                <h2 class="text-xs font-semibold uppercase tracking-wide text-secondary">Quick Overview</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-secondary">Total Documents</dt>
                        <dd class="font-medium text-secondary-dark">{{ $docsTotal }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-secondary">Submitted</dt>
                        <dd class="font-medium text-secondary-dark">{{ $submittedCount }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-secondary">Pending</dt>
                        <dd class="font-medium text-warning">{{ $pendingCount }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-secondary">Approved</dt>
                        <dd class="font-medium text-success">{{ $approvedCount }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-secondary">Rejected</dt>
                        <dd class="font-medium text-danger">{{ $rejectedCount }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-secondary">Training Videos</dt>
                        <dd class="font-medium text-secondary-dark">{{ $videosDone }}/{{ $videosTotal }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-secondary">Current Stage</dt>
                        <dd><x-badge :classes="$stageBadge['classes']">{{ $stageBadge['label'] }}</x-badge></dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-secondary">Last Activity</dt>
                        <dd class="font-medium text-secondary-dark">{{ $lastActivity }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-admin-layout>
