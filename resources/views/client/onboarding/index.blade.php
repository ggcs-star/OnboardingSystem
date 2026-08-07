<x-client-layout title="Onboarding">
    <!-- Alpine JS State Wrapper: Wraps the entire page content -->
    <div x-data="{ open: false, selectedProduct: null }">

        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold tracking-tight text-secondary-dark">Onboarding</h1>
            <p class="mt-2 text-sm text-secondary">
                Choose a product and give it a brand name to get started — we'll walk you through the required documents next.
            </p>
        </div>

        <!-- Onboarding Steps -->
        {{-- <x-onboarding-steps :current="1" class="mb-10" /> --}}

        <!-- Assigned Products Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse($assignedProducts as $product)
                @php
                    $existingProjects = $projectsByProduct->get($product->id) ?? collect();
                    $onboardedCount = $existingProjects->count();
                    $hasErrorForThisProduct = $errors->has('brand_name') && (int) old('product_id') === $product->id;
                @endphp

                <!-- Product Card -->
                <div class="flex h-full flex-col rounded-2xl border border-app-border bg-white p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:border-primary/30">
                    
                    <!-- Icon/Image -->
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-primary-light p-2">
                        <img src="{{ $product->imageUrl() ?: asset('favicon.png') }}" alt="{{ $product->name }}" class="h-full w-full object-contain">
                    </span>

                    <!-- Title & Badge -->
                    <div class="mt-5 flex items-start justify-between gap-2">
                        <h2 class="text-lg font-semibold text-secondary-dark">{{ $product->name }}</h2>
                        @if ($onboardedCount > 0)
                            <x-badge classes="bg-primary-light text-primary shrink-0">{{ $onboardedCount }} onboarded</x-badge>
                        @endif
                    </div>

                    <!-- Tagline & Intro -->
                    <div class="mb-6 flex-1">
                        @if ($product->tagline)
                            <p class="mt-1 text-xs font-medium uppercase tracking-wider text-primary/80">{{ $product->tagline }}</p>
                        @endif

                        @if ($product->introduction)
                            <p class="mt-3 text-sm leading-relaxed text-secondary line-clamp-3">{{ $product->introduction }}</p>
                        @endif
                    </div>

                    <!-- Onboarding Form -->
                    <div class="mt-auto border-t border-app-border pt-5">
                        <form method="POST" action="{{ route('client.onboarding.store', $product) }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div>
                                <x-input-label :for="'brand_name_' . $product->id" value="Brand Name" class="text-xs font-semibold uppercase tracking-wide text-secondary-dark" />
                                <x-text-input 
                                    :id="'brand_name_' . $product->id" 
                                    name="brand_name" 
                                    type="text"
                                    placeholder="e.g. SamacharCity"
                                    class="mt-2 w-full transition-colors focus:border-primary focus:ring-primary"
                                    :value="$hasErrorForThisProduct ? old('brand_name') : ''" 
                                    required />
                                @if ($hasErrorForThisProduct)
                                    <x-input-error :messages="$errors->get('brand_name')" class="mt-2" />
                                @endif
                            </div>

                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                                Start Onboarding
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="col-span-full flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-app-border bg-gray-50/50 py-16 px-10 text-center">
                    <svg class="mb-4 h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                    <h3 class="text-lg font-medium text-secondary-dark">No products available</h3>
                    <p class="mt-1 text-sm text-secondary">Check back soon for new offerings.</p>
                </div>
            @endforelse
        </div> 

        <!-- Other Products Section -->
        @if($otherProducts->isNotEmpty())
            <div class="mt-16 border-t border-app-border pt-12">
                <div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold tracking-tight text-secondary-dark">Other Products</h2>
                        <p class="mt-2 text-sm text-secondary">
                            Interested in these products? Send us an inquiry and our team will contact you.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($otherProducts as $product)
                        <div class="flex h-full flex-col rounded-2xl border border-app-border bg-white p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:border-gray-300">
                            
                            <!-- Icon/Image (Same as Onboarding) -->
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gray-100 p-2">
                                <img src="{{ $product->imageUrl() ?: asset('favicon.png') }}" alt="{{ $product->name }}" class="h-full w-full object-contain">
                            </span>

                            <!-- Title (Same as Onboarding, without badge) -->
                            <div class="mt-5 flex items-start justify-between gap-2">
                                <h3 class="text-lg font-semibold text-secondary-dark">{{ $product->name }}</h3>
                            </div>

                            <!-- Tagline & Intro (Same as Onboarding) -->
                            <div class="mb-6 flex-1">
                                @if ($product->tagline)
                                    <p class="mt-1 text-xs font-medium uppercase tracking-wider text-secondary">{{ $product->tagline }}</p>
                                @endif

                                @if ($product->introduction)
                                    <p class="mt-3 text-sm leading-relaxed text-secondary line-clamp-3">{{ $product->introduction }}</p>
                                @endif
                            </div>

                            <!-- Button Section -->
                            <div class="mt-auto border-t border-app-border pt-5">
                                <button
                                    type="button"
                                    x-on:click="selectedProduct = {{ $product->id }}; open = true;"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                                >
                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                    </svg>
                                    Show Interest
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 🚀 INQUIRY MODAL (Smart Form) -->
        <div 
            x-show="open" 
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 sm:p-0 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div 
                @click.away="open = false" 
                class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl sm:p-8"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-secondary-dark">Product Inquiry</h2>
                    <button @click="open = false" type="button" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('client.product.inquiry.store') }}" class="space-y-4">
                    @csrf
                    
                    <!-- Alpine Data -->
                    <input type="hidden" name="product_id" x-bind:value="selectedProduct">

                    <!-- Prefilled Hidden Data (Clean UX) -->
                    <input type="hidden" name="contact_person" value="{{ auth()->user()->name }}">
                    <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                    
                    <!-- Adjust these relationships based on your actual DB structure -->
                    <input type="hidden" name="company" value="{{ auth()->user()->client->company_name ?? '' }}">
                    <input type="hidden" name="phone" value="{{ auth()->user()->client->phone ?? '' }}">

                    <div>
                        <x-input-label for="message" value="Reason for Interest / Any Questions?" class="text-sm font-semibold text-secondary-dark" />
                        <textarea 
                            id="message" 
                            name="message" 
                            rows="4" 
                            placeholder="Tell us what you're looking for..."
                            class="mt-2 w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                            required></textarea>
                    </div>

                    <div class="rounded-lg bg-blue-50 p-3 mt-4">
                        <p class="text-xs text-blue-700">
                            <span class="font-semibold">Note:</span> Your contact details ({{ auth()->user()->email }}) will automatically be attached to this inquiry for our team to reach out.
                        </p>
                    </div>

                    <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button type="button" @click="open = false" class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex w-full sm:w-auto items-center justify-center rounded-xl bg-primary px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                            Submit Inquiry
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- End of Modal -->

    </div> <!-- End of Alpine JS Wrapper -->
</x-client-layout>