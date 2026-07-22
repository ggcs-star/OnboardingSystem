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
                            <x-badge :classes="$salesEmployee->status === 'active' ? 'bg-success-light text-success' : 'bg-secondary-light text-secondary-dark'" dot>
                                {{ ucfirst($salesEmployee->status) }}
                            </x-badge>
                        </td>
                        <td class="px-4 py-3 text-secondary-dark">{{ $salesEmployee->projects_count }}</td>
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
                        <td colspan="6" class="px-4 py-8 text-center text-secondary">No sales employees added yet.</td>
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
