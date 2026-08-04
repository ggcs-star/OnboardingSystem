@php
    $availableClientsForJs = $availableClients->map(fn ($client) => [
        'id' => $client->id,
        'name' => $client->company_name,
        'email' => $client->user->email ?? '—',
    ]);
@endphp

<x-admin-layout :title="$lmsProduct->name . ' — Assigned Clients'">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.lms.products.index') }}" class="hover:text-primary">Documentation</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <a href="{{ route('admin.lms.products.show', $lmsProduct) }}" class="hover:text-primary">{{ $lmsProduct->name }}</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">Assigned Clients</span>
    </nav>

    <div class="mt-3 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                <x-icon name="book-open" class="w-6 h-6" />
            </span>
            <div>
                <h1 class="text-xl font-semibold text-secondary-dark">{{ $lmsProduct->name }}</h1>
                <p class="text-sm text-secondary">Clients assigned here can read this product's documentation on the client portal.</p>
            </div>
        </div>

        <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'assign-lms-client')">
            <x-icon name="plus" class="w-4 h-4" />
            Assign New Client
        </x-primary-button>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-app-border bg-white">
        <table class="min-w-full divide-y divide-app-border text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                    <th class="px-4 py-3">Client</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-app-border">
                @forelse ($assignedClients as $client)
                    <tr>
                        <td class="px-4 py-3 font-medium text-secondary-dark">{{ $client->company_name }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $client->user->email ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('admin.lms.products.clients.toggle', ['lmsProduct' => $lmsProduct, 'client' => $client]) }}"
                                onsubmit="return confirm('Remove {{ $client->company_name }}\'s access to this documentation?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 rounded-md border border-danger/30 bg-danger-light px-2.5 py-1.5 text-xs font-medium text-danger hover:border-danger/60 hover:bg-danger/20">
                                    <x-icon name="x" class="w-3.5 h-3.5" />
                                    Remove Access
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-10 text-center text-sm text-secondary">
                            No clients assigned yet. Click "Assign New Client" to give a client access to this documentation.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-modal name="assign-lms-client" maxWidth="lg">
        <form method="POST" action="{{ route('admin.lms.products.clients.bulk-assign', $lmsProduct) }}"
            x-data="{
                search: '',
                selected: [],
                clients: {{ \Illuminate\Support\Js::from($availableClientsForJs) }},
                get filtered() {
                    const q = this.search.trim().toLowerCase();
                    if (! q) return this.clients;
                    return this.clients.filter(c => c.name.toLowerCase().includes(q) || c.email.toLowerCase().includes(q));
                },
                get allFilteredSelected() {
                    return this.filtered.length > 0 && this.filtered.every(c => this.selected.includes(c.id));
                },
                toggleSelectAll(checked) {
                    const ids = this.filtered.map(c => c.id);
                    this.selected = checked
                        ? [...new Set([...this.selected, ...ids])]
                        : this.selected.filter(id => ! ids.includes(id));
                },
            }">
            @csrf

            <div class="p-6 pb-0">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-secondary-dark">Assign New Client</h2>
                    <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>
                <p class="mt-1 text-sm text-secondary">Only clients who don't already have access to this documentation are listed below.</p>

                <div class="relative mt-4">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary" />
                    <input type="text" x-model="search" placeholder="Search by client name or email..."
                        class="w-full rounded-lg border-app-border pl-10 text-sm shadow-sm focus:border-primary focus:ring-primary">
                </div>

                @if ($availableClients->isNotEmpty())
                    <label class="mt-3 flex items-center gap-2 text-xs font-medium text-secondary-dark">
                        <input type="checkbox" x-bind:checked="allFilteredSelected" x-on:change="toggleSelectAll($event.target.checked)"
                            class="rounded border-app-border text-primary focus:ring-primary">
                        <span>Select all <span x-show="search" x-cloak>matching</span></span>
                        <span x-show="selected.length > 0" x-cloak class="text-secondary">— <span x-text="selected.length"></span> selected</span>
                    </label>
                @endif
            </div>

            <div class="mt-3 max-h-80 divide-y divide-app-border overflow-y-auto border-y border-app-border">
                <template x-for="client in filtered" :key="client.id">
                    <label class="flex cursor-pointer items-center justify-between gap-3 px-6 py-2.5 hover:bg-surface-alt">
                        <span class="flex items-center gap-3">
                            <input type="checkbox" name="client_ids[]" x-model.number="selected" :value="client.id"
                                class="rounded border-app-border text-primary focus:ring-primary">
                            <span>
                                <span class="block font-medium text-secondary-dark" x-text="client.name"></span>
                                <span class="block text-xs text-secondary" x-text="client.email"></span>
                            </span>
                        </span>
                    </label>
                </template>

                @if ($availableClients->isEmpty())
                    <p class="px-6 py-6 text-center text-sm text-secondary">Every client is already assigned to this product.</p>
                @else
                    <p class="px-6 py-6 text-center text-sm text-secondary" x-show="filtered.length === 0" x-cloak>No clients match "<span x-text="search"></span>".</p>
                @endif
            </div>

            <div class="flex items-center justify-end gap-3 p-6 pt-4">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button type="submit" x-bind:disabled="selected.length === 0">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span x-text="selected.length > 0 ? `Assign ${selected.length} Client${selected.length > 1 ? 's' : ''}` : 'Assign Selected'"></span>
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</x-admin-layout>
