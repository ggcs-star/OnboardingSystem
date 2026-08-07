<x-admin-layout title="Documentation">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">Documentation</h1>
            <p class="mt-1 text-sm text-secondary">
                Manage product documentation — categories, sub-categories and articles clients can read once assigned
            </p>
        </div>

        <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-lms-product')">
            <x-icon name="plus" class="w-4 h-4" />
            Add Product
        </x-primary-button>
    </div>

    <form method="GET" class="mt-6 rounded-xl border border-app-border bg-white p-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-12">
            <div class="min-w-0 lg:col-span-8" x-data="{ search: @js(request('search', '')) }">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Search Product</label>
                <div class="relative">
                    <x-icon name="search"
                        class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary" />
                    <input type="text" name="search" x-model="search"
                        placeholder="Search by product name or tagline..."
                        class="w-full rounded-lg border-app-border pl-10 pr-9 text-sm shadow-sm focus:border-primary focus:ring-primary">
                    <a x-show="search.length > 0" x-cloak
                        href="{{ route('admin.lms.products.index') }}"
                        title="Clear all filters"
                        class="absolute right-2.5 top-1/2 flex h-5 w-5 -translate-y-1/2 items-center justify-center rounded-full bg-surface-alt text-secondary hover:bg-app-border hover:text-secondary-dark">
                        <x-icon name="x" class="h-3 w-3" />
                    </a>
                </div>
            </div>

            <div class="min-w-0 lg:col-span-4">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Status</label>
                <select name="status" onchange="this.form.submit()"
                    class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                    <option value="">All Statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
        </div>

        <button type="submit" class="hidden"></button>

        @if (request()->anyFilled(['search', 'status']))
            <div class="mt-4 flex items-center gap-3">
                <a href="{{ route('admin.lms.products.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-primary/30 px-4 py-2 text-sm font-medium text-primary hover:bg-primary-light">
                    <x-icon name="refresh-cw" class="w-4 h-4" />
                    Reset Filters
                </a>
            </div>
        @endif
    </form>

    <div class="mt-6 overflow-x-auto rounded-xl border border-app-border bg-white">
        <table class="min-w-full divide-y divide-app-border text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                    <th class="px-4 py-3">Product</th>
                    <th class="px-4 py-3">Categories</th>
                    <th class="px-4 py-3">Sub-categories</th>
                    <th class="px-4 py-3">Articles</th>
                    <th class="px-4 py-3">Clients</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-app-border">
                @forelse ($lmsProducts as $lmsProduct)
                    @php
                        $productLogoUrl = optional($lmsProduct->product)->imageUrl();
                    @endphp
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.lms.products.show', $lmsProduct) }}" class="flex items-center gap-3 hover:text-primary">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-primary-light p-1.5">
                                    <img src="{{ $productLogoUrl ?: asset('favicon.png') }}" alt="{{ $lmsProduct->name }}" class="h-full w-full object-contain">
                                </span>
                                <span>
                                    <span class="block font-medium text-secondary-dark">{{ $lmsProduct->name }}</span>
                                    @if ($lmsProduct->tagline)
                                        <span class="mt-0.5 block text-xs text-secondary">{{ $lmsProduct->tagline }}</span>
                                    @endif
                                </span>
                            </a>
                        </td>
                        <td class="px-4 py-3 font-medium text-secondary-dark">{{ $lmsProduct->categories_count }}</td>
                        <td class="px-4 py-3 font-medium text-secondary-dark">{{ $lmsProduct->sub_categories_count }}</td>
                        <td class="px-4 py-3 font-medium text-secondary-dark">{{ $lmsProduct->articles_count }}</td>
                        <td class="px-4 py-3 font-medium text-secondary-dark">{{ $lmsProduct->purchasedClientsCount() }}</td>
                        <td class="px-4 py-3">
                            @php
                                $lmsStatusColorClass = $lmsProduct->active ? 'text-success' : 'text-danger';
                                $lmsStatusSelectClasses = $lmsProduct->active ? 'bg-success-light text-success' : 'bg-danger-light text-danger';
                            @endphp
                            <form method="POST" action="{{ route('admin.lms.products.status.update', $lmsProduct) }}">
                                @csrf
                                @method('PATCH')
                                <div class="relative inline-block">
                                    <span class="pointer-events-none absolute left-3 top-1/2 h-1.5 w-1.5 -translate-y-1/2 rounded-full bg-current {{ $lmsStatusColorClass }}"></span>
                                    <select name="active" onchange="this.form.submit()"
                                        class="appearance-none bg-none rounded-full border-0 py-1.5 pl-7 pr-7 text-xs font-medium focus:ring-2 focus:ring-primary {{ $lmsStatusSelectClasses }}">
                                        <option value="1" @selected($lmsProduct->active)>Active</option>
                                        <option value="0" @selected(! $lmsProduct->active)>Inactive</option>
                                    </select>
                                    <x-icon name="chevron-down"
                                        class="pointer-events-none absolute right-2.5 top-1/2 h-3 w-3 -translate-y-1/2 {{ $lmsStatusColorClass }}" />
                                </div>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap items-center justify-end gap-1.5">
                                <a href="{{ route('admin.lms.articles.create', $lmsProduct) }}" title="Add Article"
                                    class="inline-flex items-center justify-center rounded-md border border-primary/30 bg-primary-light p-1.5 text-primary hover:border-primary/60 hover:bg-primary/20">
                                    <x-icon name="plus" class="w-3.5 h-3.5" />
                                </a>
                                <a href="{{ route('admin.lms.products.clients.index', $lmsProduct) }}" title="Assigned Clients"
                                    class="inline-flex items-center justify-center rounded-md border border-success/30 bg-success-light p-1.5 text-success hover:border-success/60 hover:bg-success/20">
                                    <x-icon name="users" class="w-3.5 h-3.5" />
                                </a>
                                <a href="{{ route('admin.lms.products.show', ['lmsProduct' => $lmsProduct, 'tab' => 'details']) }}" title="Edit Product"
                                    class="inline-flex items-center justify-center rounded-md border border-warning/30 bg-warning-light p-1.5 text-warning hover:border-warning/60 hover:bg-warning/20">
                                    <x-icon name="edit" class="w-3.5 h-3.5" />
                                </a>
                                <a href="{{ route('admin.lms.products.preview', $lmsProduct) }}" title="View Documentation"
                                    class="inline-flex items-center justify-center rounded-md border border-chart-4/30 bg-chart-4/15 p-1.5 text-chart-4 hover:border-chart-4/60 hover:bg-chart-4/25">
                                    <x-icon name="eye" class="w-3.5 h-3.5" />
                                </a>
                                <form method="POST" action="{{ route('admin.lms.products.destroy', $lmsProduct) }}"
                                    onsubmit="return confirm('Delete this product and all of its categories, sub-categories and articles?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete Product"
                                        class="inline-flex items-center justify-center rounded-md border border-danger/30 bg-danger-light p-1.5 text-danger hover:border-danger/60 hover:bg-danger/20">
                                        <x-icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-sm text-secondary">
                            @if (request()->anyFilled(['search', 'status']))
                                No products match your filters.
                            @else
                                No products yet. Click "Add Product" to create your first one.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $lmsProducts->links() }}
    </div>

    <x-modal name="add-lms-product" :show="$errors->isNotEmpty()" focusable>
        <form method="POST" action="{{ route('admin.lms.products.store') }}" class="p-6" enctype="multipart/form-data">
            @csrf

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-secondary-dark">Add Product</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <div class="mt-6 space-y-5">
                <div>
                    <x-input-label for="product_id" value="Product *" class="uppercase text-xs tracking-wide" />
                    <select id="product_id" name="product_id" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary" required autofocus>
                        <option value="" disabled @selected(! old('product_id'))>Select a product…</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected((int) old('product_id') === $product->id)>{{ $product->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-secondary">Clients who purchase this product will automatically get access to its documentation.</p>
                    <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="tagline" value="Tagline" class="uppercase text-xs tracking-wide" />
                    <x-text-input id="tagline" name="tagline" class="mt-1.5" placeholder="Short description" :value="old('tagline')" />
                    <x-input-error :messages="$errors->get('tagline')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" value="Description" class="uppercase text-xs tracking-wide" />
                    <textarea id="description" name="description" rows="3" placeholder="Describe what this documentation covers..."
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="image" value="Product Image" class="uppercase text-xs tracking-wide" />
                    <input id="image" name="image" type="file" accept="image/*"
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm file:mr-3 file:rounded-md file:border-0 file:bg-surface-alt file:px-3 file:py-1.5 file:text-sm" />
                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button>Create Product</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-admin-layout>
