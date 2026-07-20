@php $setting = $product->renewalSetting; @endphp

<div class="rounded-xl border border-app-border bg-white p-6">
    <h2 class="text-lg font-semibold text-secondary-dark">Renewal Settings</h2>
    <p class="text-sm text-secondary">
        Default renewal terms applied to new projects created under this product
    </p>

    <form method="POST" action="{{ route('admin.products.renewal-settings.update', $product) }}" class="mt-6 space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <x-input-label for="default_plan_duration_months" value="Default Plan Duration (months)" class="text-xs uppercase tracking-wide text-secondary" />
                <x-text-input id="default_plan_duration_months" name="default_plan_duration_months" type="number" min="1" class="mt-1.5"
                    placeholder="e.g. 12" :value="old('default_plan_duration_months', $setting?->default_plan_duration_months)" />
                <x-input-error :messages="$errors->get('default_plan_duration_months')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="default_renewal_amount" value="Default Renewal Amount" class="text-xs uppercase tracking-wide text-secondary" />
                <x-text-input id="default_renewal_amount" name="default_renewal_amount" type="number" min="0" step="0.01" class="mt-1.5"
                    placeholder="e.g. 15000" :value="old('default_renewal_amount', $setting?->default_renewal_amount)" />
                <x-input-error :messages="$errors->get('default_renewal_amount')" class="mt-2" />
            </div>
        </div>

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
