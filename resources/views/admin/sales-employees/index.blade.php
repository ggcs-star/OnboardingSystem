<x-admin-layout title="Sales Team">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">Sales Team</h1>
            <p class="mt-1 text-sm text-secondary">Salespeople who bring in clients — track who sold which brand and how to reach them</p>
        </div>

        <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-sales-employee')">
            <x-icon name="plus" class="w-4 h-4" />
            Add Salesperson
        </x-primary-button>
    </div>

    <form method="GET" action="{{ route('admin.sales-employees.index') }}" class="mt-6 rounded-xl border border-app-border bg-white p-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-12">
            <div class="min-w-0 lg:col-span-5">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Search Salesperson</label>
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary" />
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by name, phone or email..."
                        class="w-full rounded-lg border-app-border pl-10 text-sm shadow-sm focus:border-primary focus:ring-primary">
                </div>
            </div>

            <div class="min-w-0 lg:col-span-3">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Product</label>
                <select name="product" onchange="this.form.submit()"
                    class="w-full min-w-0 rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                    <option value="">All Products</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" @selected(request('product') == $product->id)>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="min-w-0 lg:col-span-2">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Status</label>
                <select name="status" onchange="this.form.submit()"
                    class="w-full min-w-0 rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                    <option value="">All Statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>

            <div class="flex min-w-0 items-end lg:col-span-2">
                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark">
                    <x-icon name="search" class="w-4 h-4" />
                    Search
                </button>
            </div>
        </div>

        @if (request()->anyFilled(['search', 'product', 'status']))
            <div class="mt-4 flex items-center gap-3">
                <a href="{{ route('admin.sales-employees.index') }}"
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
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Phone</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Products Sold</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-app-border">
                @forelse ($salesEmployees as $salesEmployee)
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.sales-employees.show', $salesEmployee) }}" class="flex items-center gap-3 hover:text-primary">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-light text-xs font-semibold text-primary">
                                    {{ Str::substr($salesEmployee->name, 0, 1) }}
                                </span>
                                <span class="font-medium text-secondary-dark">{{ $salesEmployee->name }}</span>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-secondary">{{ $salesEmployee->phone }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $salesEmployee->email ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $salesStatusColorClass = $salesEmployee->status === 'active' ? 'text-success' : 'text-danger';
                                $salesStatusSelectClasses = $salesEmployee->status === 'active' ? 'bg-success-light text-success' : 'bg-danger-light text-danger';
                            @endphp
                            <form method="POST" action="{{ route('admin.sales-employees.status.update', $salesEmployee) }}">
                                @csrf
                                @method('PATCH')
                                <div class="relative inline-block">
                                    <span class="pointer-events-none absolute left-3 top-1/2 h-1.5 w-1.5 -translate-y-1/2 rounded-full bg-current {{ $salesStatusColorClass }}"></span>
                                    <select name="status" onchange="this.form.submit()"
                                        class="appearance-none bg-none rounded-full border-0 py-1.5 pl-7 pr-7 text-xs font-medium focus:ring-2 focus:ring-primary {{ $salesStatusSelectClasses }}">
                                        <option value="active" @selected($salesEmployee->status === 'active')>Active</option>
                                        <option value="inactive" @selected($salesEmployee->status !== 'active')>Inactive</option>
                                    </select>
                                    <x-icon name="chevron-down"
                                        class="pointer-events-none absolute right-2.5 top-1/2 h-3 w-3 -translate-y-1/2 {{ $salesStatusColorClass }}" />
                                </div>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            @php $soldProducts = $salesEmployee->projects->pluck('product')->filter()->unique('id'); @endphp
                            @if ($soldProducts->isNotEmpty())
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($soldProducts as $product)
                                        <x-badge classes="bg-secondary-light text-secondary-dark">{{ $product->name }}</x-badge>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-secondary">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.sales-employees.show', $salesEmployee) }}"
                                class="inline-flex items-center justify-center rounded-lg border border-app-border p-2 text-secondary hover:bg-surface-alt hover:text-primary"
                                title="View details">
                                <x-icon name="eye" class="w-4 h-4" />
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-secondary">
                            @if (request()->anyFilled(['search', 'product', 'status']))
                                No sales employees match these filters.
                            @else
                                No sales employees added yet.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-modal name="add-sales-employee" :show="$errors->any()" focusable>
        <form method="POST" action="{{ route('admin.sales-employees.store') }}" class="p-6">
            @csrf

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-secondary-dark">Add Salesperson</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-input-label for="name" value="Name *" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="name" name="name" class="mt-1.5" :value="old('name')" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="phone" value="Contact Number *" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="phone" name="phone" class="mt-1.5" :value="old('phone')" required />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" value="Email" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="email" name="email" type="email" class="mt-1.5" :value="old('email')" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="notes" value="Notes" class="text-xs uppercase tracking-wide" />
                    <textarea id="notes" name="notes" rows="3" placeholder="Territory, joining date, or any other sales info..."
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button>Add Salesperson</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-admin-layout>
