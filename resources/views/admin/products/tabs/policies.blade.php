<div>
    <h2 class="text-lg font-semibold text-secondary-dark">Product Policies</h2>
    <p class="text-sm text-secondary">Manage Privacy Policy, Terms &amp; Conditions, Support Policy, and Refund Policy</p>
</div>

<div class="mt-6 space-y-4" x-data="{ open: {{ $product->policies->first()?->id ?? 'null' }} }">
    @forelse ($product->policies as $policy)
        <div class="rounded-xl border border-app-border bg-white">
            <button type="button" @click="open = (open === {{ $policy->id }} ? null : {{ $policy->id }})"
                class="flex w-full items-center justify-between gap-3 px-5 py-4 text-left">
                <span class="flex items-center gap-2 font-medium text-secondary-dark">
                    <x-icon name="shield" class="w-4 h-4 text-primary" />
                    {{ $policy->title }}
                    <span class="text-xs font-normal text-secondary">Updated {{ $policy->updated_at?->format('d-M-Y') }}</span>
                </span>
                <x-icon name="chevron-down" class="w-4 h-4 text-secondary transition"
                    x-bind:class="open === {{ $policy->id }} && '-rotate-180'" />
            </button>

            <div x-show="open === {{ $policy->id }}" x-cloak class="border-t border-app-border px-5 py-5">
                <form method="POST" action="{{ route('admin.products.policies.update', ['product' => $product, 'policy' => $policy]) }}">
                    @csrf
                    @method('PUT')

                    <textarea name="content" rows="10"
                        class="w-full rounded-lg border-app-border font-mono text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('content', $policy->content) }}</textarea>
                    <x-input-error :messages="$errors->get('content')" class="mt-2" />

                    <div class="mt-4 flex items-center gap-3">
                        <x-primary-button>Save Policy</x-primary-button>
                        <span class="text-xs text-secondary">Supports markdown formatting</span>
                    </div>
                </form>
            </div>
        </div>
    @empty
        <div class="rounded-xl border border-dashed border-app-border bg-white p-8 text-center text-sm text-secondary">
            No policies configured yet.
        </div>
    @endforelse
</div>
