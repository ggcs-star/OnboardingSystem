@php
    $tabs = [
        'overview' => ['label' => 'Overview', 'icon' => 'box'],
        'projects' => ['label' => 'Projects', 'icon' => 'folder'],
        'documents' => ['label' => 'Documents', 'icon' => 'file-text'],
    ];
    $statusBadge = $client->status === 'active' ? 'bg-success-light text-success' : ($client->status === 'blocked' ? 'bg-danger-light text-danger' : 'bg-warning-light text-warning');
@endphp

<x-admin-layout :title="$client->company_name">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.clients.index') }}" class="hover:text-primary">Clients</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">{{ $client->company_name }}</span>
    </nav>

    <div class="mt-3 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary-light text-lg font-semibold text-primary">
                {{ Str::substr($client->company_name, 0, 1) }}
            </span>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-semibold text-secondary-dark">{{ $client->company_name }}</h1>
                    <x-badge :classes="$statusBadge" dot>{{ ucfirst($client->status) }}</x-badge>
                </div>
                <p class="text-sm text-secondary">{{ $client->user?->email }}</p>
            </div>
        </div>

        <span class="shrink-0 rounded-full bg-surface-alt px-3 py-1 text-xs font-medium text-secondary">
            {{ $client->projects->count() }} product{{ $client->projects->count() === 1 ? '' : 's' }} onboarded
        </span>
    </div>

    <div class="mt-6 border-b border-app-border">
        <nav class="-mb-px flex gap-6 overflow-x-auto">
            @foreach ($tabs as $key => $tab)
                <x-tab-link
                    :href="route('admin.clients.show', ['client' => $client, 'tab' => $key])"
                    :active="$activeTab === $key"
                    :icon="$tab['icon']"
                >
                    {{ $tab['label'] }}
                </x-tab-link>
            @endforeach
        </nav>
    </div>

    <div class="mt-6">
        @if ($activeTab === 'overview')
            <div class="rounded-xl border border-app-border bg-white p-6">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary">Client Details</h2>
                <div class="mt-4 grid grid-cols-2 gap-4 text-sm sm:grid-cols-3 lg:grid-cols-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-secondary">Company Name</p>
                        <p class="mt-1 font-medium text-secondary-dark">{{ $client->owner_name ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-secondary">Email</p>
                        <p class="mt-1 font-medium text-secondary-dark">{{ $client->user?->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-secondary">Contact Number</p>
                        <p class="mt-1 font-medium text-secondary-dark">{{ $client->phone ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-secondary">Location</p>
                        <p class="mt-1 font-medium text-secondary-dark">{{ collect([$client->city, $client->state, $client->country])->filter()->implode(', ') ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-secondary">Address</p>
                        <p class="mt-1 font-medium text-secondary-dark">{{ $client->address ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-secondary">Onboarded</p>
                        <p class="mt-1 font-medium text-secondary-dark">{{ $client->created_at->format('d-M-Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($projectsByProduct as $productGroup)
                    <div class="rounded-xl border border-app-border bg-white" x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                        <button type="button" x-on:click="open = !open"
                            class="flex w-full items-center justify-between gap-4 rounded-t-xl bg-surface-alt px-5 py-4 text-left">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                                    <x-icon name="box" class="w-5 h-5" />
                                </span>
                                <div>
                                    <p class="font-semibold text-secondary-dark">{{ $productGroup->product->name }}</p>
                                    <p class="text-xs text-secondary">{{ $productGroup->projects->count() }} brand{{ $productGroup->projects->count() === 1 ? '' : 's' }}</p>
                                </div>
                            </div>
                            <x-icon name="chevron-down" class="w-4 h-4 shrink-0 text-secondary transition-transform" x-bind:class="open && 'rotate-180'" />
                        </button>

                        <div x-show="open" x-cloak class="divide-y divide-app-border border-t border-app-border">
                            @foreach ($productGroup->projects as $row)
                                @php
                                    $project = $row->project;
                                    $renewal = $project->renewal;
                                    $stageBadge = project_stage_badge($project->current_stage, $project->status);
                                @endphp
                                <div class="p-6">
                                    <div class="flex items-center justify-between">
                                        <p class="font-semibold text-secondary-dark">{{ $project->brand_name ?? $project->project_name }}</p>
                                        <x-badge :classes="$stageBadge['classes']" dot>{{ $stageBadge['label'] }}</x-badge>
                                    </div>

                                    <div class="mt-5 grid grid-cols-1 gap-4 border-t border-app-border pt-5 sm:grid-cols-2">
                                        <div>
                                            <p class="text-xs uppercase tracking-wide text-secondary">Client Contact for {{ $project->brand_name ?? $project->project_name }}</p>
                                            <p class="mt-1 text-sm font-medium text-secondary-dark">{{ $project->contactName() }}</p>
                                            <p class="text-sm text-secondary">{{ $project->contactPhone() ?: 'No contact number on file' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs uppercase tracking-wide text-secondary">Sold By</p>
                                            <form method="POST" action="{{ route('admin.projects.sales-employee.update', $project) }}" class="mt-1 flex items-center gap-2">
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
                                            @if ($project->salesEmployee)
                                                <p class="mt-1 text-xs text-secondary">Chosen by the client during onboarding.</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-5 border-t border-app-border pt-5">
                                        @if (! $renewal || ! $renewal->product_subscription_plan_id)
                                            <p class="text-sm text-secondary">No subscription plan chosen yet.</p>
                                        @else
                                            @php
                                                $renewalBadge = renewal_status_badge($renewalStatuses[$project->id]);
                                                $paid = $renewal->amountPaid();
                                                $due = $renewal->amountDue();
                                            @endphp
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-secondary-dark">{{ $renewal->plan_name }}</p>
                                                @if ($renewal->go_live_date)
                                                    <x-badge :classes="$renewalBadge['classes']" dot>{{ $renewalBadge['label'] }}</x-badge>
                                                @else
                                                    <x-badge classes="bg-warning-light text-warning" dot>Not yet activated</x-badge>
                                                @endif
                                            </div>

                                            <div class="mt-3 grid grid-cols-2 gap-4 text-sm sm:grid-cols-4">
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
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                        This client hasn't onboarded any products yet.
                    </div>
                @endforelse
            </div>
        @elseif ($activeTab === 'projects')
            @if ($projectsByProduct->isEmpty())
                <div class="rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                    This client hasn't onboarded any products yet.
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($projectsByProduct as $productGroup)
                        <div class="rounded-xl border border-app-border bg-white" x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                            <button type="button" x-on:click="open = !open"
                                class="flex w-full items-center justify-between gap-4 rounded-t-xl bg-surface-alt px-5 py-4 text-left">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                                        <x-icon name="box" class="w-5 h-5" />
                                    </span>
                                    <div>
                                        <p class="font-semibold text-secondary-dark">{{ $productGroup->product->name }}</p>
                                        <p class="text-xs text-secondary">{{ $productGroup->projects->count() }} brand{{ $productGroup->projects->count() === 1 ? '' : 's' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="hidden w-44 sm:block">
                                        <x-charts.meter label="Training videos" icon="play-circle" :done="$productGroup->videosDone" :total="$productGroup->videosTotal" />
                                    </div>
                                    <x-icon name="chevron-down" class="w-4 h-4 shrink-0 text-secondary transition-transform" x-bind:class="open && 'rotate-180'" />
                                </div>
                            </button>

                            <div x-show="open" x-cloak class="grid grid-cols-1 gap-4 border-t border-app-border p-5 lg:grid-cols-2">
                                @foreach ($productGroup->projects as $row)
                                    @php
                                        $project = $row->project;
                                        $stageBadge = project_stage_badge($project->current_stage, $project->status);
                                    @endphp
                                    <div class="rounded-xl border border-app-border bg-surface-alt/40 p-5">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <p class="text-base font-semibold text-primary">{{ $project->brand_name ?? $project->project_name }}</p>
                                                <p class="text-xs text-secondary">{{ $productGroup->product->name }}</p>
                                            </div>
                                            <x-badge :classes="$stageBadge['classes']" dot>{{ $stageBadge['label'] }}</x-badge>
                                        </div>

                                        <div class="mt-4">
                                            <x-charts.meter label="Documents submitted" icon="file-text" :done="$row->docsDone" :total="$row->docsTotal" />
                                        </div>

                                        <div class="mt-4 border-t border-app-border pt-4">
                                            <p class="text-xs uppercase tracking-wide text-secondary">Client Support Contact</p>
                                            <p class="mt-1 text-sm font-medium text-secondary-dark">{{ $project->contactName() }}</p>
                                            <p class="text-sm text-secondary">{{ $project->contactPhone() ?: 'No contact number on file' }}</p>
                                        </div>

                                        <a href="{{ route('admin.clients.show', ['client' => $client, 'tab' => 'documents']) }}" title="View Documents"
                                            class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-dark">
                                            <x-icon name="eye" class="w-4 h-4" />
                                            View Documents
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            @if ($products->isEmpty())
                <div class="rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                    This client hasn't onboarded any products yet.
                </div>
            @else
                <div class="space-y-3">
                    @include('admin.documents.partials.product-groups', ['products' => $products])
                </div>
            @endif
        @endif
    </div>
</x-admin-layout>
