@php
    $hasPricing = $plans->isNotEmpty();
    $isActivated = $renewal && $renewal->go_live_date;

    $billingCycleLabel = function (int $months) {
        return match ($months) {
            1 => 'billed monthly',
            3 => 'billed quarterly',
            6 => 'billed half-yearly',
            12 => 'billed yearly',
            default => 'billed every ' . $months . ' ' . Str::plural('month', $months),
        };
    };
@endphp

<x-client-layout title="Onboarding">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Onboarding — {{ $project->product->name }}</h1>
        <p class="mt-1 text-sm text-secondary">Choose a subscription plan and log your payment.</p>
    </div>

    <x-onboarding-steps :current="3" class="mt-8" />

    <div class="mt-8">
        @if (! $hasPricing)
            <div class="rounded-xl border border-dashed border-app-border bg-white p-8 text-center text-sm text-secondary">
                No subscription plan is required for {{ $project->product->name }}.
            </div>
        @else
            @if ($isActivated)
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-success/20 bg-success-light px-4 py-3 text-sm text-success">
                    <x-icon name="check" class="mt-0.5 w-4 h-4 shrink-0" />
                    <div>
                        <p class="font-medium">Subscription active!</p>
                        <p class="mt-0.5 opacity-90">{{ $renewal->go_live_date->format('d-M-Y') }} — {{ $renewal->expiry_date->format('d-M-Y') }}</p>
                    </div>
                </div>
            @endif

            @if (! $renewal || ! $renewal->product_subscription_plan_id)
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($plans as $plan)
                        <div class="rounded-xl border border-app-border bg-white p-6">
                            <h2 class="text-base font-semibold text-secondary-dark">{{ $plan->name }}</h2>
                            <p class="mt-1 text-2xl font-semibold text-secondary-dark">₹{{ number_format($plan->amount, 2) }}</p>
                            <p class="text-xs text-secondary">{{ $billingCycleLabel($plan->duration_months) }}</p>
                            <form method="POST" action="{{ route('client.onboarding.subscription.plan', $project) }}" class="mt-5">
                                @csrf
                                <input type="hidden" name="subscription_plan_id" value="{{ $plan->id }}">
                                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-dark">
                                    Choose {{ $plan->name }}
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                @php
                    $paid = $renewal->amountPaid();
                    $due = $renewal->amountDue();
                @endphp
                <div class="rounded-xl border border-app-border bg-white p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-secondary-dark">{{ $renewal->plan_name }}</h2>
                        <x-badge classes="bg-surface-alt text-secondary">₹{{ number_format($renewal->renewal_amount, 2) }} total</x-badge>
                    </div>

                    <div class="mt-5">
                        <div class="mb-1.5 flex items-center justify-between text-xs">
                            <span class="font-medium text-secondary-dark">₹{{ number_format($paid, 2) }} paid</span>
                            <span class="text-secondary">₹{{ number_format($due, 2) }} left</span>
                        </div>
                        <x-charts.meter label="Payment progress" :done="(int) round($paid)" :total="(int) round($renewal->renewal_amount)" />
                    </div>

                    @if ($due > 0)
                        <form method="POST" action="{{ route('client.onboarding.subscription.payment', $project) }}" class="mt-6 space-y-4 border-t border-app-border pt-6">
                            @csrf
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div>
                                    <x-input-label for="amount" value="Amount *" class="text-xs uppercase tracking-wide" />
                                    <x-text-input id="amount" name="amount" type="number" min="0.01" step="0.01" max="{{ $due }}" class="mt-1.5" :value="old('amount', $due)" required />
                                    <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="payment_date" value="Payment Date *" class="text-xs uppercase tracking-wide" />
                                    <x-text-input id="payment_date" name="payment_date" type="date" class="mt-1.5" :value="old('payment_date', now()->format('Y-m-d'))" required />
                                    <x-input-error :messages="$errors->get('payment_date')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="payment_mode" value="Payment Mode" class="text-xs uppercase tracking-wide" />
                                    <select id="payment_mode" name="payment_mode" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                                        <option value="">Select</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="UPI">UPI</option>
                                        <option value="Card">Card</option>
                                        <option value="Cheque">Cheque</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <x-primary-button>Submit Payment</x-primary-button>
                            </div>
                        </form>
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
                </div>
            @endif
        @endif
    </div>

    <div class="mt-6 flex justify-end">
        <a href="{{ route('client.dashboard') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-dark">
            Go to Dashboard
        </a>
    </div>
</x-client-layout>
