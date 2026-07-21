<x-client-layout title="Onboarding">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Onboarding</h1>
        <p class="mt-1 text-sm text-secondary">Choose a product to get started — we'll walk you through the required documents next.</p>
    </div>

    <x-onboarding-steps :current="1" class="mt-8" />

    <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @forelse ($products as $product)
            @php $onboardedProject = $onboardedProjects->get($product->id); @endphp
            <div class="flex flex-col rounded-xl border border-app-border bg-white p-5">
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-primary-light text-primary">
                    @if ($product->imageUrl())
                        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="h-full w-full rounded-lg object-cover">
                    @else
                        <x-icon name="box" class="w-5 h-5" />
                    @endif
                </span>

                <h2 class="mt-4 text-base font-semibold text-secondary-dark">{{ $product->name }}</h2>
                @if ($product->tagline)
                    <p class="text-xs text-secondary">{{ $product->tagline }}</p>
                @endif

                @if ($product->introduction)
                    <p class="mt-3 text-sm text-secondary line-clamp-3">{{ $product->introduction }}</p>
                @endif

                <div class="mt-5">
                    @if ($onboardedProject)
                        <div class="mb-2">
                            <x-badge :classes="$onboardedProject->isOnboardingComplete() ? 'bg-success-light text-success' : 'bg-warning-light text-warning'" dot>
                                {{ $onboardedProject->isOnboardingComplete() ? 'Onboarded' : 'Onboarding in progress' }}
                            </x-badge>
                        </div>
                        <a href="{{ route('client.onboarding.documents', $onboardedProject) }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-app-border px-4 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                            <x-icon name="settings" class="w-4 h-4" />
                            Manage
                        </a>
                    @else
                        <form method="POST" action="{{ route('client.onboarding.store', $product) }}">
                            @csrf
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-dark">
                                Start Onboarding
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                No products are available yet. Please check back soon.
            </div>
        @endforelse
    </div>
</x-client-layout>
