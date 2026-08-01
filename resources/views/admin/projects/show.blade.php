@php
    $stageBadge = project_stage_badge($project->current_stage, $project->status);
@endphp

<x-admin-layout :title="$project->brand_name ?? $project->project_name">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.projects.index') }}" class="hover:text-primary">Ongoing Projects</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">{{ $project->brand_name ?? $project->project_name }}</span>
    </nav>

    <div class="mt-3 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                <x-icon name="box" class="w-5 h-5" />
            </span>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-semibold text-secondary-dark">{{ $project->brand_name ?? $project->project_name }}</h1>
                    <x-badge :classes="$stageBadge['classes']" dot>{{ $stageBadge['label'] }}</x-badge>
                </div>
                <p class="text-sm text-secondary">
                    {{ $project->product->name }} ·
                    <a href="{{ route('admin.clients.show', $project->client) }}" class="hover:text-primary">{{ $project->client->company_name }}</a>
                </p>
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-app-border bg-white p-6">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary">Pipeline Stage</h2>
                <div class="mt-4">
                    <x-project-timeline :stage="$project->current_stage" :status="$project->status" />
                </div>

                <form method="POST" action="{{ route('admin.projects.stage.update', $project) }}" class="mt-5 flex items-center gap-2">
                    @csrf
                    @method('PATCH')
                    <select name="stage" class="w-full max-w-xs rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                        @foreach (\App\Models\Project::STAGES as $key => $label)
                            <option value="{{ $key }}" @selected($project->current_stage === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark">
                        Update Stage
                    </button>
                </form>
            </div>

            <div class="rounded-xl border border-app-border bg-white p-6">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary">Documents</h2>
                <div class="mt-4 space-y-3">
                    @include('admin.documents.partials.product-groups', ['products' => $products])
                </div>
            </div>

        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-app-border bg-white p-6">
                <h2 class="text-xs font-semibold uppercase tracking-wide text-secondary">Progress</h2>
                <div class="mt-4 space-y-4">
                    <x-charts.meter label="Documents submitted" icon="file-text" :done="$docsDone" :total="$docsTotal" />
                    <x-charts.meter label="Training videos" icon="play-circle" :done="$videosDone" :total="$videosTotal" />
                </div>
            </div>

            <div class="rounded-xl border border-app-border bg-white p-6">
                <h2 class="text-xs font-semibold uppercase tracking-wide text-secondary">Client Support Contact</h2>
                <p class="mt-2 text-sm font-medium text-secondary-dark">{{ $project->contactName() }}</p>
                <p class="text-sm text-secondary">{{ $project->contactPhone() ?: 'No contact number on file' }}</p>
            </div>

            <div class="rounded-xl border border-app-border bg-white p-6">
                <h2 class="text-xs font-semibold uppercase tracking-wide text-secondary">Sold By</h2>
                <form method="POST" action="{{ route('admin.projects.sales-employee.update', $project) }}" class="mt-3">
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

            <div class="rounded-xl border border-app-border bg-white p-6">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary">Subscription &amp; Renewal</h2>
                @if (! $project->renewal || ! $project->renewal->product_subscription_plan_id)
                    <p class="mt-3 text-sm text-secondary">No subscription plan chosen yet.</p>
                @else
                    @php
                        $renewal = $project->renewal;
                        $renewalBadge = renewal_status_badge($renewalStatus);
                        $paid = $renewal->amountPaid();
                    @endphp
                    <div class="mt-3 flex items-center justify-between">
                        <p class="text-sm font-medium text-secondary-dark">{{ $renewal->plan_name }}</p>
                        @if ($renewal->go_live_date)
                            <x-badge :classes="$renewalBadge['classes']" dot>{{ $renewalBadge['label'] }}</x-badge>
                        @else
                            <x-badge classes="bg-warning-light text-warning" dot>Not yet activated</x-badge>
                        @endif
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-4 text-sm">
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
    </div>
</x-admin-layout>
