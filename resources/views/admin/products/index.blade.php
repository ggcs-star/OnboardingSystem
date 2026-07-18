<x-admin-layout title="Products">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">Products</h1>
            <p class="mt-1 text-sm text-secondary">
                Manage your software products — document templates, training, policies and renewal settings
            </p>
        </div>

        <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-product')">
            <x-icon name="plus" class="w-4 h-4" />
            Add Product
        </x-primary-button>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Total Products" :value="$stats['total_products']" hint="in system" />
        <x-stat-card label="Active" :value="$stats['active_products']" hint="live products" />
        <x-stat-card label="Total Projects" :value="$stats['total_projects']" hint="across all products" />
        <x-stat-card label="Total Clients" :value="$stats['total_clients']" hint="using products" />
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        @forelse ($products as $product)
            <div class="flex flex-col rounded-xl border border-app-border bg-white p-6">
                <div class="flex items-start justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-primary-light text-primary">
                        <x-icon name="box" class="w-5 h-5" />
                    </span>
                    <x-badge :classes="status_badge_classes($product->active)" dot>
                        {{ $product->active ? 'Active' : 'Inactive' }}
                    </x-badge>
                </div>

                <h2 class="mt-4 text-lg font-semibold text-secondary-dark">{{ $product->name }}</h2>
                @if ($product->tagline)
                    <p class="text-sm text-secondary">{{ $product->tagline }}</p>
                @endif

                @if ($product->introduction)
                    <p class="mt-3 text-sm text-secondary line-clamp-2">{{ $product->introduction }}</p>
                @endif

                @if ($product->category)
                    <div class="mt-3">
                        <x-badge classes="bg-surface-alt text-secondary">
                            <x-icon name="tag" class="w-3 h-3" />
                            {{ $product->category }}
                        </x-badge>
                    </div>
                @endif

                <div class="mt-5 grid grid-cols-3 divide-x divide-app-border border-y border-app-border py-4 text-center">
                    <div>
                        <p class="text-lg font-semibold text-secondary-dark">{{ $product->projectsCount() }}</p>
                        <p class="text-xs text-secondary">Projects</p>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-secondary-dark">{{ $product->document_fields_count }}</p>
                        <p class="text-xs text-secondary">Doc Fields</p>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-secondary-dark">{{ $product->training_count }}</p>
                        <p class="text-xs text-secondary">Videos</p>
                    </div>
                </div>

                <a href="{{ route('admin.products.show', $product) }}"
                    class="mt-5 inline-flex items-center justify-center gap-2 rounded-lg border border-app-border px-4 py-2.5 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                    <x-icon name="eye" class="w-4 h-4" />
                    Manage Product
                </a>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                No products yet. Click "Add Product" to create your first one.
            </div>
        @endforelse
    </div>

    <x-modal name="add-product" :show="$errors->isNotEmpty()" focusable>
        <form method="POST" action="{{ route('admin.products.store') }}" class="p-6" enctype="multipart/form-data">
            @csrf

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-secondary-dark">Add New Product</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <div class="mt-6 space-y-5">
                <div>
                    <x-input-label for="name" value="Product Name *" class="uppercase text-xs tracking-wide" />
                    <x-text-input id="name" name="name" class="mt-1.5" placeholder="e.g. LocalPulse" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="tagline" value="Tagline" class="uppercase text-xs tracking-wide" />
                    <x-text-input id="tagline" name="tagline" class="mt-1.5" placeholder="Short description" :value="old('tagline')" />
                    <x-input-error :messages="$errors->get('tagline')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="category" value="Category" class="uppercase text-xs tracking-wide" />
                    <x-text-input id="category" name="category" class="mt-1.5" placeholder="e.g. Media & Publishing" :value="old('category')" />
                    <x-input-error :messages="$errors->get('category')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="introduction" value="Description" class="uppercase text-xs tracking-wide" />
                    <textarea id="introduction" name="introduction" rows="3" placeholder="Describe what this product does..."
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('introduction') }}</textarea>
                    <x-input-error :messages="$errors->get('introduction')" class="mt-2" />
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button>Create Product</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-admin-layout>
