@php
    $tabs = [
        'details' => ['label' => 'Details', 'icon' => 'edit'],
        'categories' => ['label' => 'Categories & Articles', 'icon' => 'file-text'],
    ];
@endphp

<x-admin-layout title="Edit Product">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.lms.products.index') }}" class="hover:text-primary">Documentation</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">{{ $lmsProduct->name }}</span>
    </nav>

    <div class="mt-3 flex items-start gap-4">
        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
            <x-icon name="book-open" class="w-6 h-6" />
        </span>
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-semibold text-secondary-dark">Edit Product</h1>
                <x-badge :classes="status_badge_classes($lmsProduct->active)" dot>
                    {{ $lmsProduct->active ? 'Active' : 'Inactive' }}
                </x-badge>
            </div>
            <p class="text-sm text-secondary">Update product details, categories and documentation.</p>
        </div>
    </div>

    <div class="mt-6 border-b border-app-border">
        <nav class="-mb-px flex gap-6 overflow-x-auto">
            @foreach ($tabs as $key => $tab)
                <x-tab-link
                    :href="route('admin.lms.products.show', ['lmsProduct' => $lmsProduct, 'tab' => $key])"
                    :active="$activeTab === $key"
                    :icon="$tab['icon']"
                >
                    {{ $tab['label'] }}
                </x-tab-link>
            @endforeach
        </nav>
    </div>

    <div class="mt-6">
        @include('admin.lms.products.tabs.' . $activeTab)
    </div>
</x-admin-layout>
