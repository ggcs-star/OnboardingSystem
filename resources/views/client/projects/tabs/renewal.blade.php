@php $renewal = $project->renewal; @endphp

<div class="rounded-xl border border-app-border bg-white p-6">
    @if (! $renewal || ! $renewal->product_subscription_plan_id)
        <div class="text-center text-sm text-secondary">
            <x-icon name="refresh-cw" class="mx-auto mb-3 h-8 w-8 text-secondary/50" />
            You haven't chosen a subscription plan yet.
            <a href="{{ route('client.onboarding.subscription', $project) }}" class="mt-2 inline-block font-medium text-primary hover:underline">Choose a plan</a>
        </div>
    @else
        @php
            $badge = renewal_status_badge($renewalStatus);
            $paid = $renewal->amountPaid();
            $due = $renewal->amountDue();
        @endphp

        @if ($renewalStatus === 'expiring')
            <div class="mb-4 flex items-center gap-2 rounded-lg bg-warning-light px-4 py-3 text-sm text-warning">
                <x-icon name="alert" class="w-4 h-4" />
                Your plan is expiring soon on {{ $renewal->expiry_date->format('d-M-Y') }}. Please contact us to renew.
            </div>
        @elseif ($renewalStatus === 'expired')
            <div class="mb-4 flex items-center gap-2 rounded-lg bg-danger-light px-4 py-3 text-sm text-danger">
                <x-icon name="alert" class="w-4 h-4" />
                Your plan expired on {{ $renewal->expiry_date->format('d-M-Y') }}. Please contact us to renew.
            </div>
        @endif

        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-secondary-dark">{{ $renewal->plan_name }}</h2>
            @if ($renewal->go_live_date)
                <x-badge :classes="$badge['classes']" dot>{{ $badge['label'] }}</x-badge>
            @else
                <x-badge classes="bg-warning-light text-warning" dot>Not yet activated</x-badge>
            @endif
        </div>

        <div class="mt-5 grid grid-cols-2 gap-5 sm:grid-cols-3">
            <div>
                <p class="text-xs uppercase tracking-wide text-secondary">Start Date</p>
                <p class="mt-1 font-medium text-secondary-dark">{{ $renewal->go_live_date?->format('d-M-Y') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-secondary">Expiry</p>
                <p class="mt-1 font-medium text-secondary-dark">{{ $renewal->expiry_date?->format('d-M-Y') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-secondary">Plan Amount</p>
                <p class="mt-1 font-medium text-secondary-dark">₹{{ number_format($renewal->renewal_amount, 2) }}</p>
            </div>
        </div>

        <div class="mt-5">
            <div class="mb-1.5 flex items-center justify-between text-xs">
                <span class="font-medium text-secondary-dark">₹{{ number_format($paid, 2) }} paid</span>
                <span class="text-secondary">₹{{ number_format($due, 2) }} left</span>
            </div>
            <x-charts.meter label="Payment progress" :done="(int) round($paid)" :total="(int) round($renewal->renewal_amount)" />
        </div>

        @if (! $renewal->go_live_date)
            <a href="{{ route('client.onboarding.subscription', $project) }}" class="mt-5 inline-block text-sm font-medium text-primary hover:underline">
                Continue subscription setup
            </a>
        @endif

        @if ($renewal->history->isNotEmpty())
            <div class="mt-6 overflow-x-auto border-t border-app-border pt-6">
                <table class="min-w-full divide-y divide-app-border text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                            <th class="py-2 pr-4">Date</th>
                            <th class="py-2 pr-4">Amount</th>
                            <th class="py-2 pr-4">Mode</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-app-border">
                        @foreach ($renewal->history as $payment)
                            <tr>
                                <td class="py-2 pr-4 text-secondary">{{ $payment->payment_date->format('d-M-Y') }}</td>
                                <td class="py-2 pr-4 font-medium text-secondary-dark">₹{{ number_format($payment->amount, 2) }}</td>
                                <td class="py-2 pr-4 text-secondary">{{ $payment->payment_mode ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif
</div>
