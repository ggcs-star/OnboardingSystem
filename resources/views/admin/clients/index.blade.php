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

    <form method="GET" class="mt-6 rounded-xl border border-app-border bg-white p-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-12">
            <div class="min-w-0 lg:col-span-4" x-data="{ search: @js(request('search', '')) }">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Search Client</label>
                <div class="relative">
                    <x-icon name="search"
                        class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary" />
                    <input type="text" name="search" x-model="search"
                        placeholder="Search by client name, email or contact..."
                        class="w-full rounded-lg border-app-border pl-10 pr-9 text-sm shadow-sm focus:border-primary focus:ring-primary">
                    <a x-show="search.length > 0" x-cloak
                        href="{{ route('admin.clients.index') }}"
                        title="Clear all filters"
                        class="absolute right-2.5 top-1/2 flex h-5 w-5 -translate-y-1/2 items-center justify-center rounded-full bg-surface-alt text-secondary hover:bg-app-border hover:text-secondary-dark">
                        <x-icon name="x" class="h-3 w-3" />
                    </a>
                </div>
            </div>

            <div class="min-w-0 lg:col-span-4">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Onboarded Date Range</label>
                <div class="flex min-w-0 items-center gap-2">
                    <div class="relative min-w-0 flex-1">
                        <x-icon name="calendar" class="pointer-events-none absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-secondary" />
                        <input type="date" name="onboarded_from" value="{{ request('onboarded_from') }}"
                            class="w-full min-w-0 rounded-lg border-app-border pl-8 text-sm shadow-sm focus:border-primary focus:ring-primary">
                    </div>
                    <span class="shrink-0 text-secondary">–</span>
                    <div class="relative min-w-0 flex-1">
                        <x-icon name="calendar" class="pointer-events-none absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-secondary" />
                        <input type="date" name="onboarded_to" value="{{ request('onboarded_to') }}"
                            class="w-full min-w-0 rounded-lg border-app-border pl-8 text-sm shadow-sm focus:border-primary focus:ring-primary">
                    </div>
                </div>
            </div>

            <div class="min-w-0 lg:col-span-2">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Pending Docs</label>
                <select name="docs"
                    class="w-full min-w-0 rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                    <option value="">All Documents</option>
                    <option value="complete" @selected(request('docs') === 'complete')>Fully Submitted</option>
                    <option value="pending" @selected(request('docs') === 'pending')>Has Pending Docs</option>
                </select>
            </div>

            <div class="min-w-0 lg:col-span-2">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Status</label>
                <select name="status"
                    class="w-full min-w-0 rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                    <option value="">All Statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="blocked" @selected(request('status') === 'blocked')>Inactive</option>
                </select>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark">
                <x-icon name="filter" class="w-4 h-4" />
                Search
            </button>

            @if (request()->anyFilled(['search', 'status', 'onboarded_from', 'onboarded_to', 'docs']))
                <a href="{{ route('admin.clients.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-primary/30 px-4 py-2 text-sm font-medium text-primary hover:bg-primary-light">
                    <x-icon name="refresh-cw" class="w-4 h-4" />
                    Reset Filters
                </a>
            @endif
        </div>
    </form>

    <div class="mt-6 overflow-x-auto rounded-xl border border-app-border bg-white">
        <table class="min-w-full divide-y divide-app-border text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                    <th class="px-4 py-3">Client</th>
                    <th class="px-4 py-3">Contact Details</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Projects</th>
                    <th class="px-4 py-3">Onboarded</th>
                    <th class="px-4 py-3">Pending Docs</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-app-border">
                @forelse ($clients as $client)
                    @php
                        $activeProjects = $client->projects->where('status', 'active')->count();
                        $docsSummary = $client->documentsSummary();
                        $docsBreakdownRows = collect($docsSummary->breakdown)->sortBy(['product', 'brand'])->values();
                        $avatarPalette = ['bg-chart-1/15 text-chart-1', 'bg-chart-2/15 text-chart-2', 'bg-chart-3/15 text-chart-3', 'bg-chart-4/15 text-chart-4', 'bg-chart-5/15 text-chart-5'];
                        $avatarClasses = $avatarPalette[$client->id % count($avatarPalette)];
                        $docsPercent = $docsSummary->total > 0 ? (int) round(($docsSummary->done / $docsSummary->total) * 100) : 0;
                    @endphp
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.clients.show', $client) }}"
                                class="flex items-center gap-3 hover:text-primary">
                                <span
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-semibold {{ $avatarClasses }}">
                                    {{ Str::substr($client->company_name, 0, 1) }}
                                </span>
                                <span>
                                    <span class="block font-medium text-secondary-dark">{{ $client->company_name }}</span>
                                    @if ($client->owner_name)
                                        <span class="mt-0.5 flex items-center gap-1 text-xs text-secondary">
                                            <x-icon name="users" class="h-3 w-3" />
                                            {{ $client->owner_name }}
                                        </span>
                                    @endif
                                </span>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-secondary">
                            <span class="flex items-center gap-1.5">
                                <x-icon name="mail" class="h-3.5 w-3.5 shrink-0 text-secondary" />
                                {{ $client->user?->email ?: '—' }}
                            </span>
                            <span class="mt-1 flex items-center gap-1.5">
                                <x-icon name="phone" class="h-3.5 w-3.5 shrink-0 text-secondary" />
                                {{ $client->phone ?: '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.clients.status.toggle', $client) }}">
                                @csrf
                                @method('PATCH')
                                @php
                                    $statusColorClass = $client->status === 'active' ? 'text-success' : 'text-danger';
                                    $statusSelectClasses = $client->status === 'active' ? 'bg-success-light text-success' : 'bg-danger-light text-danger';
                                @endphp
                                <div class="relative inline-block">
                                    <select name="status" onchange="this.form.submit()"
                                        class="appearance-none bg-none rounded-lg border-0 py-1.5 pl-3 pr-7 text-xs font-medium focus:ring-2 focus:ring-primary {{ $statusSelectClasses }}">
                                        <option value="active" @selected($client->status === 'active')>● Active</option>
                                        <option value="blocked" @selected($client->status !== 'active')>● Inactive</option>
                                    </select>
                                    <x-icon name="chevron-down"
                                        class="pointer-events-none absolute right-2.5 top-1/2 h-3 w-3 -translate-y-1/2 {{ $statusColorClass }}" />
                                </div>
                            </form>
                        </td>
                        @php $projectsTotal = $client->projects->count(); @endphp
                        <td class="px-4 py-3 text-secondary-dark">
                            @if ($projectsTotal === 0)
                                <span class="flex items-center gap-1 whitespace-nowrap text-sm text-secondary">
                                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-secondary"></span>
                                    0 Active
                                </span>
                            @elseif ($activeProjects === $projectsTotal)
                                <span class="flex items-center gap-1 whitespace-nowrap text-sm font-medium text-success">
                                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-success"></span>
                                    {{ $activeProjects }} Active
                                </span>
                            @else
                                <span class="flex items-center gap-1 whitespace-nowrap text-sm font-medium text-warning">
                                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-warning"></span>
                                    {{ $activeProjects }} of {{ $projectsTotal }} Active
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-secondary-dark">
                            <span class="flex items-center gap-1.5">
                                <x-icon name="calendar" class="h-3.5 w-3.5 shrink-0 text-secondary" />
                                {{ $client->created_at->format('d-M-Y') }}
                            </span>
                            <span class="mt-0.5 block text-xs text-secondary">Onboarded Date</span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($docsSummary->total === 0)
                                <span class="text-secondary">—</span>
                            @else
                                <div
                                    @if ($docsBreakdownRows->isNotEmpty())
                                        x-data="{
                                            open: false,
                                            top: 0,
                                            left: 0,
                                            toggle() {
                                                const r = $el.getBoundingClientRect();
                                                this.top = r.bottom + window.scrollY + 4;
                                                this.left = r.left + window.scrollX;
                                                this.open = !this.open;
                                                if (! this.open) return;
                                                this.$nextTick(() => {
                                                    const panel = this.$refs.panel;
                                                    if (! panel) return;
                                                    const pr = panel.getBoundingClientRect();
                                                    if (pr.bottom > window.innerHeight) {
                                                        this.top = Math.max(8, r.top + window.scrollY - pr.height - 4);
                                                    }
                                                    const overflowRight = pr.right - window.innerWidth;
                                                    if (overflowRight > 0) {
                                                        this.left = Math.max(8, this.left - overflowRight - 8);
                                                    }
                                                });
                                            },
                                        }"
                                        x-on:click.outside="open = false"
                                        x-on:keydown.escape.window="open = false"
                                    @endif
                                    class="relative inline-block w-36">
                                    @if ($docsBreakdownRows->isNotEmpty())
                                        <button type="button" x-on:click="toggle()" class="block w-full text-left">
                                            <span class="flex items-baseline gap-1 font-semibold {{ $docsSummary->pending > 0 ? 'text-warning' : 'text-success' }}">
                                                {{ $docsSummary->done }} of {{ $docsSummary->total }}
                                                <span class="ml-auto text-xs font-medium text-secondary">{{ $docsPercent }}%</span>
                                            </span>
                                            <span class="mt-1 block h-1.5 w-full overflow-hidden rounded-full bg-surface-alt">
                                                <span class="block h-full rounded-full {{ $docsSummary->pending > 0 ? 'bg-warning' : 'bg-success' }}" style="width: {{ $docsPercent }}%"></span>
                                            </span>
                                            <span class="mt-1 block text-xs text-secondary">
                                                {{ $docsSummary->pending > 0 ? $docsSummary->pending.' documents pending' : 'All documents uploaded' }}
                                            </span>
                                        </button>
                                        <template x-teleport="body">
                                            <div x-ref="panel" x-show="open" x-cloak
                                                :style="`top: ${top}px; left: ${left}px;`"
                                                class="fixed z-50 w-96 max-w-[90vw] overflow-hidden rounded-lg border border-app-border bg-white shadow-lg">
                                                <div class="flex items-center justify-between border-b border-app-border px-3 py-2">
                                                    <span class="text-xs font-semibold text-secondary-dark">Pending documents by product</span>
                                                    <button type="button" x-on:click="open = false" class="text-secondary hover:text-secondary-dark">
                                                        <x-icon name="x" class="w-3.5 h-3.5" />
                                                    </button>
                                                </div>
                                                <table class="w-full text-xs">
                                                    <thead>
                                                        <tr class="bg-surface-alt text-left text-[11px] font-semibold uppercase tracking-wide text-secondary">
                                                            <th class="px-3 py-2">Product</th>
                                                            <th class="px-3 py-2">Brand</th>
                                                            <th class="px-3 py-2 text-right">Pending</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-app-border">
                                                        @foreach ($docsBreakdownRows as $item)
                                                            <tr>
                                                                <td class="px-3 py-1.5 font-medium text-secondary-dark">{{ $item['product'] }}</td>
                                                                <td class="px-3 py-1.5 text-secondary-dark">{{ $item['brand'] ?? '—' }}</td>
                                                                <td class="px-3 py-1.5 text-right font-medium text-warning">{{ $item['pending'] }}/{{ $item['total'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </template>
                                    @else
                                        <span class="block font-semibold text-success">{{ $docsSummary->done }} of {{ $docsSummary->total }}</span>
                                        <span class="mt-1 block h-1.5 w-full overflow-hidden rounded-full bg-surface-alt">
                                            <span class="block h-full rounded-full bg-success" style="width: 100%"></span>
                                        </span>
                                        <span class="mt-1 block text-xs text-secondary">All documents uploaded</span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.clients.show', $client) }}" title="View Details"
                                    class="inline-flex items-center justify-center rounded-md border border-primary/30 bg-primary-light p-1.5 text-primary hover:border-primary/60 hover:bg-primary/20">
                                    <x-icon name="eye" class="w-3.5 h-3.5" />
                                </a>
                                <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-client-{{ $client->id }}')"
                                    title="Edit Client"
                                    class="inline-flex items-center justify-center rounded-md border border-primary/30 bg-primary-light p-1.5 text-primary hover:border-primary/60 hover:bg-primary/20">
                                    <x-icon name="edit" class="w-3.5 h-3.5" />
                                </button>
                                <form method="POST" action="{{ route('admin.clients.destroy', $client) }}"
                                    onsubmit="return confirm('Delete this client? This also removes their projects and login account.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete Client"
                                        class="inline-flex items-center justify-center rounded-md border border-danger/30 bg-danger-light p-1.5 text-danger hover:border-danger/60 hover:bg-danger/20">
                                        <x-icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </form>
                            </div>

                            <x-modal name="edit-client-{{ $client->id }}" focusable>
                                <form method="POST" action="{{ route('admin.clients.update', $client) }}" class="p-6 text-left">
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
                                            <x-input-label for="edit_company_name_{{ $client->id }}" value="Client Name *" class="text-xs uppercase tracking-wide" />
                                            <x-text-input id="edit_company_name_{{ $client->id }}" name="company_name" class="mt-1.5" :value="$client->company_name" required />
                                        </div>

                                        <div>
                                            <x-input-label for="edit_email_{{ $client->id }}" value="Client Email *" class="text-xs uppercase tracking-wide" />
                                            <x-text-input id="edit_email_{{ $client->id }}" name="email" type="email" class="mt-1.5" :value="$client->user?->email" required />
                                        </div>

                                        <div x-data="{ showPassword: false }">
                                            <x-input-label for="edit_password_{{ $client->id }}" value="Client Password" class="text-xs uppercase tracking-wide" />
                                            <div class="relative mt-1.5">
                                                <x-text-input id="edit_password_{{ $client->id }}" name="password" :type="'password'"
                                                    x-bind:type="showPassword ? 'text' : 'password'" class="pr-10"
                                                    placeholder="Leave blank to keep current password" />
                                                <button type="button" x-on:click="showPassword = !showPassword"
                                                    class="absolute inset-y-0 right-0 flex w-10 items-center justify-center text-secondary hover:text-secondary-dark">
                                                    <x-icon x-show="!showPassword" name="eye" class="!w-4 !h-4" />
                                                    <x-icon x-show="showPassword" name="eye-off" class="!w-4 !h-4" x-cloak />
                                                </button>
                                            </div>
                                        </div>

                                        <div>
                                            <x-input-label for="edit_owner_name_{{ $client->id }}" value="Company Name" class="text-xs uppercase tracking-wide" />
                                            <x-text-input id="edit_owner_name_{{ $client->id }}" name="owner_name" class="mt-1.5" :value="$client->owner_name" />
                                        </div>

                                        <div>
                                            <x-input-label for="edit_phone_{{ $client->id }}" value="Contact Number" class="text-xs uppercase tracking-wide" />
                                            <x-text-input id="edit_phone_{{ $client->id }}" name="phone" class="mt-1.5" :value="$client->phone" />
                                        </div>

                                        <div>
                                            <x-input-label for="edit_address_{{ $client->id }}" value="Address" class="text-xs uppercase tracking-wide" />
                                            <x-text-input id="edit_address_{{ $client->id }}" name="address" class="mt-1.5" :value="$client->address" />
                                        </div>

                                        <div>
                                            <x-input-label for="edit_city_{{ $client->id }}" value="City" class="text-xs uppercase tracking-wide" />
                                            <x-text-input id="edit_city_{{ $client->id }}" name="city" class="mt-1.5" :value="$client->city" />
                                        </div>

                                        <div>
                                            <x-input-label for="edit_state_{{ $client->id }}" value="State" class="text-xs uppercase tracking-wide" />
                                            <x-text-input id="edit_state_{{ $client->id }}" name="state" class="mt-1.5" :value="$client->state" />
                                        </div>

                                        <div>
                                            <x-input-label for="edit_country_{{ $client->id }}" value="Country" class="text-xs uppercase tracking-wide" />
                                            <x-text-input id="edit_country_{{ $client->id }}" name="country" class="mt-1.5" :value="$client->country" />
                                        </div>
                                    </div>

                                    <div class="mt-8 flex justify-end gap-3">
                                        <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                        <x-primary-button>Save Changes</x-primary-button>
                                    </div>
                                </form>
                            </x-modal>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-secondary">No clients yet.</td>
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
                    <x-text-input id="company_name" name="company_name" class="mt-1.5" :value="old('company_name')"
                        required />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" value="Client Email *" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="email" name="email" type="email" class="mt-1.5" :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div x-data="{ showPassword: false }">
                    <x-input-label for="password" value="Client Password *" class="text-xs uppercase tracking-wide" />
                    <div class="relative mt-1.5">
                        <x-text-input id="password" name="password" :type="'password'"
                            x-bind:type="showPassword ? 'text' : 'password'" class="pr-10"
                            placeholder="Shared with the client to log in" required />
                        <button type="button" x-on:click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 flex w-10 items-center justify-center text-secondary hover:text-secondary-dark">
                            <x-icon x-show="!showPassword" name="eye" class="!w-4 !h-4" />
                            <x-icon x-show="showPassword" name="eye-off" class="!w-4 !h-4" x-cloak />
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="owner_name" value="Company Name" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="owner_name" name="owner_name" class="mt-1.5" :value="old('owner_name')" />
                    <x-input-error :messages="$errors->get('owner_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="phone" value="Contact Number" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="phone" name="phone" class="mt-1.5" :value="old('phone')" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
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
                <div class="sm:col-span-2">
                    <x-input-label value="Assign Products" class="text-xs uppercase tracking-wide" />

                    <div class="mt-2 grid grid-cols-2 gap-3">

                        @foreach($products as $product)

                            <label class="flex items-center gap-2 rounded-lg border p-3 cursor-pointer hover:bg-gray-50">

                                <input type="checkbox" name="products[]" value="{{ $product->id }}"
                                    class="rounded border-gray-300">

                                <span>{{ $product->name }}</span>

                            </label>

                        @endforeach

                    </div>

                    <x-input-error :messages="$errors->get('products')" class="mt-2" />
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button>Add Client</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-admin-layout>