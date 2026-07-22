@php
    $setting = $product->renewalSetting;
    $plan = $product->subscriptionPlans->first();
@endphp

<div class="rounded-xl border border-app-border bg-white p-6">
    <h2 class="text-lg font-semibold text-secondary-dark">Subscription Plan</h2>
    <p class="text-sm text-secondary">The plan clients are billed when subscribing to this product during onboarding.</p>

    <div class="mt-6 grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
        <div>
            <p class="text-xs uppercase tracking-wide text-secondary">Plan Name</p>
            <p class="mt-1 font-medium text-secondary-dark">{{ $plan?->name ?? '—' }}</p>
        </div>
        <div>
            <p class="text-xs uppercase tracking-wide text-secondary">Duration</p>
            <p class="mt-1 font-medium text-secondary-dark">{{ $plan ? $plan->duration_months . ' ' . Str::plural('month', $plan->duration_months) : '—' }}</p>
        </div>
        <div>
            <p class="text-xs uppercase tracking-wide text-secondary">Amount</p>
            <p class="mt-1 font-medium text-secondary-dark">{{ $plan ? '₹' . number_format($plan->amount, 2) : '—' }}</p>
        </div>
    </div>
</div>

<div class="mt-6 rounded-xl border border-app-border bg-white p-6">
    <h2 class="text-lg font-semibold text-secondary-dark">Reminders</h2>
    <p class="text-sm text-secondary">Expiry reminder behaviour for all plans on this product</p>

    <form method="POST" action="{{ route('admin.products.renewal-settings.update', $product) }}" class="mt-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <x-input-label for="reminder_before_days" value="Send Reminder Before Expiry (days)" class="text-xs uppercase tracking-wide text-secondary" />
            <x-text-input id="reminder_before_days" name="reminder_before_days" type="number" min="0" class="mt-1.5"
                placeholder="e.g. 30" :value="old('reminder_before_days', $setting?->reminder_before_days ?? 30)" />
            <x-input-error :messages="$errors->get('reminder_before_days')" class="mt-2" />
        </div>

        <label class="flex items-center gap-2 text-sm text-secondary-dark">
            <input type="checkbox" name="auto_renew_reminder" value="1"
                @checked(old('auto_renew_reminder', $setting?->auto_renew_reminder ?? true))
                class="rounded border-app-border text-primary focus:ring-primary">
            Automatically remind clients before their plan expires
        </label>

        <div class="pt-2">
            <x-primary-button>Save Settings</x-primary-button>
        </div>
    </form>
</div>
