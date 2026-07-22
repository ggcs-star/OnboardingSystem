<x-client-layout title="Onboarding">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Onboarding</h1>
        <p class="mt-1 text-sm text-secondary">Choose a product and give it a brand name to get started — we'll walk you through the required documents next.</p>
    </div>

    <x-onboarding-steps :current="1" class="mt-8" />

    <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @forelse ($products as $product)
            @php
                $existingProjects = $projectsByProduct->get($product->id) ?? collect();
                $onboardedCount = $existingProjects->count();
                $hasErrorForThisProduct = $errors->has('brand_name') && (int) old('product_id') === $product->id;
            @endphp
            <div class="flex flex-col rounded-xl border border-app-border bg-white p-5">
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-primary-light text-primary">
                    @if ($product->imageUrl())
                        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="h-full w-full rounded-lg object-cover">
                    @else
                        <x-icon name="box" class="w-5 h-5" />
                    @endif
                </span>

                <div class="mt-4 flex items-center gap-2">
                    <h2 class="text-base font-semibold text-secondary-dark">{{ $product->name }}</h2>
                    @if ($onboardedCount > 0)
                        <x-badge classes="bg-primary-light text-primary">{{ $onboardedCount }} onboarded</x-badge>
                    @endif
                </div>
                @if ($product->tagline)
                    <p class="text-xs text-secondary">{{ $product->tagline }}</p>
                @endif

                @if ($product->introduction)
                    <p class="mt-3 text-sm text-secondary line-clamp-3">{{ $product->introduction }}</p>
                @endif

                <div class="mt-5">
                    <form method="POST" action="{{ route('client.onboarding.store', $product) }}" class="space-y-2">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div>
                            <x-input-label :for="'brand_name_' . $product->id" value="Brand Name" class="text-xs uppercase tracking-wide" />
                            <x-text-input :id="'brand_name_' . $product->id" name="brand_name" type="text" placeholder="e.g. SamacharCity" class="mt-1.5 w-full"
                                :value="$hasErrorForThisProduct ? old('brand_name') : ''" required />
                            @if ($hasErrorForThisProduct)
                                <x-input-error :messages="$errors->get('brand_name')" class="mt-1" />
                            @endif
                        </div>
                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-dark">
                            Start Onboarding
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                No products are available yet. Please check back soon.
            </div>
        @endforelse
    </div>
</x-client-layout>
