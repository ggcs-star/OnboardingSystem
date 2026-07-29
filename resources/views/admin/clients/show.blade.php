<x-admin-layout :title="$client->company_name">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.clients.index') }}" class="hover:text-primary">Clients Manage</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">{{ $client->company_name }}</span>
    </nav>

    <div class="mt-4 max-w-2xl rounded-xl border border-app-border bg-white p-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary-light text-lg font-semibold text-primary">
                    {{ Str::substr($client->company_name, 0, 1) }}
                </span>
                <div>
                    <h1 class="text-lg font-semibold text-secondary-dark">{{ $client->company_name }}</h1>
                    @php $statusBadge = $client->status === 'active' ? 'bg-success-light text-success' : 'bg-danger-light text-danger'; @endphp
                    <x-badge :classes="$statusBadge" dot>{{ $client->status === 'active' ? 'Active' : 'Inactive' }}</x-badge>
                </div>
            </div>
            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-client')" class="text-secondary hover:text-primary" title="Edit">
                <x-icon name="settings" class="w-4 h-4" />
            </button>
        </div>

        <dl class="mt-6 space-y-4 text-sm">
            <div class="flex items-center gap-2">
                <x-icon name="tag" class="w-4 h-4 text-secondary" />
                <dd class="text-secondary-dark">{{ $client->user?->email }}</dd>
            </div>
            @if ($client->owner_name)
                <div class="flex items-center gap-2">
                    <x-icon name="users" class="w-4 h-4 text-secondary" />
                    <dd class="text-secondary-dark">{{ $client->owner_name }}</dd>
                </div>
            @endif
            @if ($client->phone)
                <div class="flex items-center gap-2">
                    <x-icon name="phone" class="w-4 h-4 text-secondary" />
                    <dd class="text-secondary-dark">{{ $client->phone }}</dd>
                </div>
            @endif
            @if (collect([$client->address, $client->city, $client->state, $client->country])->filter()->isNotEmpty())
                <div class="flex items-center gap-2">
                    <x-icon name="map-pin" class="w-4 h-4 text-secondary" />
                    <dd class="text-secondary-dark">{{ collect([$client->address, $client->city, $client->state, $client->country])->filter()->implode(', ') }}</dd>
                </div>
            @endif
            <div class="flex items-center gap-2">
                <x-icon name="calendar" class="w-4 h-4 text-secondary" />
                <dd class="text-secondary-dark">Onboarded {{ $client->created_at->format('d-M-Y') }}</dd>
            </div>
        </dl>

        <div class="mt-6 flex flex-wrap items-center gap-3 border-t border-app-border pt-4">
            <a href="{{ route('admin.projects.index', ['client' => $client->id]) }}"
                class="inline-flex items-center gap-2 rounded-lg border border-app-border px-4 py-2 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="folder" class="w-4 h-4" />
                View Projects
            </a>

            <form method="POST" action="{{ route('admin.clients.status.toggle', $client) }}">
                @csrf
                @method('PATCH')
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg border border-app-border px-4 py-2 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                    <x-icon name="{{ $client->status === 'active' ? 'x' : 'check' }}" class="w-4 h-4" />
                    {{ $client->status === 'active' ? 'Deactivate' : 'Activate' }}
                </button>
            </form>

            <form method="POST" action="{{ route('admin.clients.destroy', $client) }}"
                onsubmit="return confirm('Delete this client? This also removes their projects and login account.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm font-medium text-danger hover:underline">Delete client</button>
            </form>
        </div>
    </div>

    <x-modal name="edit-client" :show="$errors->any()" focusable>
        <form method="POST" action="{{ route('admin.clients.update', $client) }}" class="p-6">
            @csrf
            @method('PUT')

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-secondary-dark">Edit Client</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-input-label for="edit_company_name" value="Client Name *" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="edit_company_name" name="company_name" class="mt-1.5" :value="old('company_name', $client->company_name)" required />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="edit_owner_name" value="Owner Name" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="edit_owner_name" name="owner_name" class="mt-1.5" :value="old('owner_name', $client->owner_name)" />
                    <x-input-error :messages="$errors->get('owner_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="edit_email" value="Client Email *" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="edit_email" name="email" type="email" class="mt-1.5" :value="old('email', $client->user?->email)" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="edit_phone" value="Contact Number" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="edit_phone" name="phone" class="mt-1.5" :value="old('phone', $client->phone)" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="edit_password" value="New Password" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="edit_password" name="password" type="password" class="mt-1.5" placeholder="Leave blank to keep current password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="edit_address" value="Address" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="edit_address" name="address" class="mt-1.5" :value="old('address', $client->address)" />
                </div>

                <div>
                    <x-input-label for="edit_city" value="City" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="edit_city" name="city" class="mt-1.5" :value="old('city', $client->city)" />
                </div>

                <div>
                    <x-input-label for="edit_state" value="State" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="edit_state" name="state" class="mt-1.5" :value="old('state', $client->state)" />
                </div>

                <div>
                    <x-input-label for="edit_country" value="Country" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="edit_country" name="country" class="mt-1.5" :value="old('country', $client->country)" />
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button>Save Changes</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-admin-layout>
