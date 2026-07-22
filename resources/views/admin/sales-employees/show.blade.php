<x-admin-layout :title="$salesEmployee->name">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.sales-employees.index') }}" class="hover:text-primary">Sales Team</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">{{ $salesEmployee->name }}</span>
    </nav>

    <div class="mt-4 flex flex-col gap-6 lg:flex-row">
        <div class="rounded-xl border border-app-border bg-white p-6 lg:w-96 lg:shrink-0">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary-light text-lg font-semibold text-primary">
                        {{ Str::substr($salesEmployee->name, 0, 1) }}
                    </span>
                    <div>
                        <h1 class="text-lg font-semibold text-secondary-dark">{{ $salesEmployee->name }}</h1>
                        <x-badge :classes="$salesEmployee->status === 'active' ? 'bg-success-light text-success' : 'bg-secondary-light text-secondary-dark'" dot>
                            {{ ucfirst($salesEmployee->status) }}
                        </x-badge>
                    </div>
                </div>
                <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-sales-employee')" class="text-secondary hover:text-primary" title="Edit">
                    <x-icon name="settings" class="w-4 h-4" />
                </button>
            </div>

            <dl class="mt-6 space-y-4 text-sm">
                <div class="flex items-center gap-2">
                    <x-icon name="phone" class="w-4 h-4 text-secondary" />
                    <dd class="text-secondary-dark">{{ $salesEmployee->phone }}</dd>
                </div>
                @if ($salesEmployee->email)
                    <div class="flex items-center gap-2">
                        <x-icon name="tag" class="w-4 h-4 text-secondary" />
                        <dd class="text-secondary-dark">{{ $salesEmployee->email }}</dd>
                    </div>
                @endif
            </dl>

            @if ($salesEmployee->notes)
                <div class="mt-6 border-t border-app-border pt-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-secondary">Notes</dt>
                    <dd class="mt-1 whitespace-pre-line text-sm text-secondary-dark">{{ $salesEmployee->notes }}</dd>
                </div>
            @endif

            <div class="mt-6 rounded-lg bg-surface-alt px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-secondary">Products Sold</p>
                <p class="mt-1 text-2xl font-semibold text-secondary-dark">{{ $salesEmployee->projects->count() }}</p>
            </div>
        </div>

        <div class="flex-1 overflow-hidden rounded-xl border border-app-border bg-white">
            <div class="border-b border-app-border px-5 py-4">
                <h2 class="font-semibold text-secondary-dark">Brands Sold</h2>
                <p class="text-xs text-secondary">Every project/brand attributed to {{ $salesEmployee->name }}, with the client's contact details</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-app-border text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                            <th class="px-5 py-3">Brand</th>
                            <th class="px-5 py-3">Product</th>
                            <th class="px-5 py-3">Client</th>
                            <th class="px-5 py-3">Client Contact</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-app-border">
                        @forelse ($salesEmployee->projects as $project)
                            <tr>
                                <td class="px-5 py-3">
                                    <a href="{{ route('admin.clients.show', $project->client) }}" class="font-medium text-secondary-dark hover:text-primary">
                                        {{ $project->brand_name ?? $project->project_name }}
                                    </a>
                                </td>
                                <td class="px-5 py-3 text-secondary">{{ $project->product->name }}</td>
                                <td class="px-5 py-3 text-secondary">{{ $project->client->company_name }}</td>
                                <td class="px-5 py-3 text-secondary">
                                    {{ $project->client->owner_name ?? '—' }}
                                    @if ($project->client->phone)
                                        <span class="block text-xs">{{ $project->client->phone }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-secondary">No brands attributed to this salesperson yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modal name="edit-sales-employee" :show="$errors->any()" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-secondary-dark">Edit Salesperson</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

        <form method="POST" action="{{ route('admin.sales-employees.update', $salesEmployee) }}">
            @csrf
            @method('PUT')

            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-input-label for="edit_name" value="Name *" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="edit_name" name="name" class="mt-1.5" :value="old('name', $salesEmployee->name)" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="edit_phone" value="Contact Number *" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="edit_phone" name="phone" class="mt-1.5" :value="old('phone', $salesEmployee->phone)" required />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="edit_email" value="Email" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="edit_email" name="email" type="email" class="mt-1.5" :value="old('email', $salesEmployee->email)" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="edit_status" value="Status *" class="text-xs uppercase tracking-wide" />
                    <select id="edit_status" name="status" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                        <option value="active" @selected(old('status', $salesEmployee->status) === 'active')>Active</option>
                        <option value="inactive" @selected(old('status', $salesEmployee->status) === 'inactive')>Inactive</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="edit_notes" value="Notes" class="text-xs uppercase tracking-wide" />
                    <textarea id="edit_notes" name="notes" rows="3"
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('notes', $salesEmployee->notes) }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button>Save Changes</x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.sales-employees.destroy', $salesEmployee) }}"
            onsubmit="return confirm('Remove this salesperson? Their sold brands will be unassigned.');" class="mt-4 border-t border-app-border pt-4">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-sm font-medium text-danger hover:underline">Remove salesperson</button>
        </form>
        </div>
    </x-modal>
</x-admin-layout>
