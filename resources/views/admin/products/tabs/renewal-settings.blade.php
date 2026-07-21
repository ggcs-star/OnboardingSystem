@php $setting = $product->renewalSetting; @endphp

<div class="rounded-xl border border-app-border bg-white p-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-secondary-dark">Subscription Plans</h2>
            <p class="text-sm text-secondary">
                Plans clients choose from when subscribing to this product during onboarding — add as many as you need (Monthly, Yearly, Quarterly, etc.)
            </p>
        </div>
        <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-subscription-plan')">
            <x-icon name="plus" class="w-4 h-4" />
            Add Plan
        </x-primary-button>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-app-border">
        <table class="min-w-full divide-y divide-app-border text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                    <th class="px-4 py-3">Plan Name</th>
                    <th class="px-4 py-3">Duration</th>
                    <th class="px-4 py-3">Amount</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-app-border">
                @forelse ($product->subscriptionPlans as $plan)
                    <tr>
                        <td class="px-4 py-3 font-medium text-secondary-dark">{{ $plan->name }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $plan->duration_months }} {{ Str::plural('month', $plan->duration_months) }}</td>
                        <td class="px-4 py-3 text-secondary">₹{{ number_format($plan->amount, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'edit-subscription-plan-{{ $plan->id }}')"
                                class="text-secondary hover:text-primary" title="Edit plan">
                                <x-icon name="settings" class="w-4 h-4" />
                            </button>
                            <form method="POST" action="{{ route('admin.products.subscription-plans.destroy', ['product' => $product, 'subscriptionPlan' => $plan]) }}"
                                class="inline" onsubmit="return confirm('Remove this plan?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-2 text-secondary hover:text-danger" title="Remove plan">
                                    <x-icon name="trash" class="w-4 h-4" />
                                </button>
                            </form>

                            <x-modal :name="'edit-subscription-plan-' . $plan->id" focusable>
                                <form method="POST" action="{{ route('admin.products.subscription-plans.update', ['product' => $product, 'subscriptionPlan' => $plan]) }}" class="p-6">
                                    @csrf
                                    @method('PUT')

                                    <div class="flex items-center justify-between">
                                        <h2 class="text-lg font-semibold text-secondary-dark">Edit Plan</h2>
                                        <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                                            <x-icon name="x" class="w-5 h-5" />
                                        </button>
                                    </div>

                                    <div class="mt-6 space-y-5">
                                        <div>
                                            <x-input-label value="Plan Name *" class="text-xs uppercase tracking-wide" />
                                            <x-text-input name="name" class="mt-1.5" :value="$plan->name" required />
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <x-input-label value="Duration (months) *" class="text-xs uppercase tracking-wide" />
                                                <x-text-input name="duration_months" type="number" min="1" class="mt-1.5" :value="$plan->duration_months" required />
                                            </div>
                                            <div>
                                                <x-input-label value="Amount *" class="text-xs uppercase tracking-wide" />
                                                <x-text-input name="amount" type="number" min="0" step="0.01" class="mt-1.5" :value="$plan->amount" required />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-8 flex justify-end gap-3">
                                        <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                        <x-primary-button>Save Changes</x-primary-button>
                                    </div>
                                </form>
                            </x-modal>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-secondary">No subscription plans yet. Click "Add Plan" to create one.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-modal name="add-subscription-plan" :show="$errors->hasAny(['name', 'duration_months', 'amount'])" focusable>
        <form method="POST" action="{{ route('admin.products.subscription-plans.store', $product) }}" class="p-6">
            @csrf

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-secondary-dark">Add Subscription Plan</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <div class="mt-6 space-y-5">
                <div>
                    <x-input-label for="plan_name" value="Plan Name *" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="plan_name" name="name" class="mt-1.5" placeholder="e.g. Monthly, Yearly, Quarterly" :value="old('name')" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="duration_months" value="Duration (months) *" class="text-xs uppercase tracking-wide" />
                        <x-text-input id="duration_months" name="duration_months" type="number" min="1" class="mt-1.5" placeholder="e.g. 1" :value="old('duration_months')" required />
                        <x-input-error :messages="$errors->get('duration_months')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="amount" value="Amount *" class="text-xs uppercase tracking-wide" />
                        <x-text-input id="amount" name="amount" type="number" min="0" step="0.01" class="mt-1.5" placeholder="e.g. 1500" :value="old('amount')" required />
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button>Add Plan</x-primary-button>
            </div>
        </form>
    </x-modal>
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
