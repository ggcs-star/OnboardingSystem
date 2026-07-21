@php
    $tabs = [
        'product-details' => ['label' => 'Product Details', 'icon' => 'box'],
        'document-fields' => ['label' => 'Document Fields', 'icon' => 'file-text'],
        'policies' => ['label' => 'Policies', 'icon' => 'shield'],
        'faqs' => ['label' => 'FAQs', 'icon' => 'help-circle'],
        'renewal-settings' => ['label' => 'Subscriptions', 'icon' => 'refresh-cw'],
    ];
@endphp

<x-admin-layout :title="$product->name">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.products.index') }}" class="hover:text-primary">Products</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">{{ $product->name }}</span>
    </nav>

    <div class="mt-3 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                <x-icon name="box" class="w-6 h-6" />
            </span>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-semibold text-secondary-dark">{{ $product->name }}</h1>
                    <x-badge :classes="status_badge_classes($product->active)" dot>
                        {{ $product->active ? 'Active' : 'Inactive' }}
                    </x-badge>
                </div>
                @if ($product->tagline)
                    <p class="text-sm text-secondary">{{ $product->tagline }}</p>
                @endif
            </div>
        </div>

        <span class="shrink-0 rounded-full bg-surface-alt px-3 py-1 text-xs font-medium text-secondary">
            {{ $projectsCount }} projects · {{ $clientsCount }} clients
        </span>
    </div>

    <div class="mt-6 border-b border-app-border">
        <nav class="-mb-px flex gap-6 overflow-x-auto">
            @foreach ($tabs as $key => $tab)
                <x-tab-link
                    :href="route('admin.products.show', ['product' => $product, 'tab' => $key])"
                    :active="$activeTab === $key"
                    :icon="$tab['icon']"
                >
                    {{ $tab['label'] }}
                </x-tab-link>
            @endforeach
        </nav>
    </div>

    <div class="mt-6">
        @include('admin.products.tabs.' . $activeTab)
    </div>
</x-admin-layout>
