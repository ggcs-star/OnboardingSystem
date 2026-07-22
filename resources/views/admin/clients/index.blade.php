<x-admin-layout title="Clients">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">Clients</h1>
            <p class="mt-1 text-sm text-secondary">Companies onboarded across all your products</p>
        </div>

        <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-client')">
            <x-icon name="plus" class="w-4 h-4" />
            Add Client
        </x-primary-button>
    </div>

    <form method="GET" class="mt-6 flex flex-col gap-3 sm:flex-row">
        <div class="relative flex-1">
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary" />
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search companies, owners, email..."
                class="w-full rounded-lg border-app-border pl-10 text-sm shadow-sm focus:border-primary focus:ring-primary">
        </div>
        <select name="status" onchange="this.form.submit()" class="rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
            <option value="">All Statuses</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="blocked" @selected(request('status') === 'blocked')>Blocked</option>
        </select>
        <button type="submit" class="hidden"></button>
    </form>

    <div class="mt-6 overflow-x-auto rounded-xl border border-app-border bg-white">
        <table class="min-w-full divide-y divide-app-border text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                    <th class="px-4 py-3">Client Name</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Location</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Projects</th>
                    <th class="px-4 py-3">Onboarded</th>
                    <th class="px-4 py-3">Pending Docs</th>
                    <th class="px-4 py-3 text-right">Docs</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-app-border">
                @forelse ($clients as $client)
                    @php
                        $activeProjects = $client->projects->where('status', 'active')->count();
                        $pendingDocs = $client->projects->flatMap->documentEntries()->where('status', '!=', 'approved')->count();
                    @endphp
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.clients.show', $client) }}" class="flex items-center gap-3 hover:text-primary">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-light text-xs font-semibold text-primary">
                                    {{ Str::substr($client->company_name, 0, 1) }}
                                </span>
                                <span class="font-medium text-secondary-dark">{{ $client->company_name }}</span>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-secondary">
                            {{ $client->user?->email }}
                        </td>
                        <td class="px-4 py-3 text-secondary">
                            {{ collect([$client->city, $client->state])->filter()->implode(', ') ?: '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @php $statusBadge = $client->status === 'active' ? 'bg-success-light text-success' : ($client->status === 'blocked' ? 'bg-danger-light text-danger' : 'bg-warning-light text-warning'); @endphp
                            <x-badge :classes="$statusBadge" dot>{{ ucfirst($client->status) }}</x-badge>
                        </td>
                        <td class="px-4 py-3 text-secondary-dark">
                            {{ $client->projects->count() }} <span class="text-xs text-secondary">({{ $activeProjects }} active)</span>
                        </td>
                        <td class="px-4 py-3 text-secondary">{{ $client->created_at->format('d-M-Y') }}</td>
                        <td class="px-4 py-3">
                            @if ($pendingDocs > 0)
                                <span class="font-medium text-warning">{{ $pendingDocs }}</span>
                            @else
                                <span class="text-success">All done</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.clients.show', ['client' => $client, 'tab' => 'documents']) }}"
                                class="inline-flex items-center justify-center rounded-lg border border-app-border p-2 text-secondary hover:bg-surface-alt hover:text-primary"
                                title="View documents">
                                <x-icon name="file-text" class="w-4 h-4" />
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-secondary">No clients yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $clients->links() }}
    </div>

    <x-modal name="add-client" :show="$errors->any()" focusable>
        <form method="POST" action="{{ route('admin.clients.store') }}" class="p-6">
            @csrf

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-secondary-dark">Add New Client</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-input-label for="company_name" value="Client Name *" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="company_name" name="company_name" class="mt-1.5" :value="old('company_name')" required />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="owner_name" value="Company Name" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="owner_name" name="owner_name" class="mt-1.5" :value="old('owner_name')" />
                    <x-input-error :messages="$errors->get('owner_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" value="Client Email *" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="email" name="email" type="email" class="mt-1.5" :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="phone" value="Contact Number" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="phone" name="phone" class="mt-1.5" :value="old('phone')" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <div class="sm:col-span-2" x-data="{ showPassword: false }">
                    <x-input-label for="password" value="Client Password *" class="text-xs uppercase tracking-wide" />
                    <div class="relative mt-1.5">
                        <x-text-input id="password" name="password" :type="'password'" x-bind:type="showPassword ? 'text' : 'password'" class="pr-10" placeholder="Shared with the client to log in" required />
                        <button type="button" x-on:click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex w-10 items-center justify-center text-secondary hover:text-secondary-dark">
                            <x-icon x-show="!showPassword" name="eye" class="!w-4 !h-4" />
                            <x-icon x-show="showPassword" name="eye-off" class="!w-4 !h-4" x-cloak />
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="address" value="Address" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="address" name="address" class="mt-1.5" :value="old('address')" />
                </div>

                <div>
                    <x-input-label for="city" value="City" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="city" name="city" class="mt-1.5" :value="old('city')" />
                </div>

                <div>
                    <x-input-label for="state" value="State" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="state" name="state" class="mt-1.5" :value="old('state')" />
                </div>

                <div>
                    <x-input-label for="country" value="Country" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="country" name="country" class="mt-1.5" :value="old('country', 'India')" />
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button>Add Client</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-admin-layout>
