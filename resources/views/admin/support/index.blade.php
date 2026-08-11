<x-admin-layout title="Support">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-secondary-dark">Support</h1>
        <p class="mt-1 text-sm text-secondary">Every support request clients have raised, across all products</p>
    </div>

    <form method="GET" action="{{ route('admin.support.index') }}" class="mb-6 rounded-xl border border-app-border bg-white p-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-12">
            <div class="min-w-0 lg:col-span-4">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Search Client / Ticket</label>
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary" />
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by client name or ticket no..."
                        class="w-full rounded-lg border-app-border pl-10 text-sm shadow-sm focus:border-primary focus:ring-primary">
                </div>
            </div>

            <div class="min-w-0 lg:col-span-2">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Category</label>
                <select name="category" onchange="this.form.submit()"
                    class="w-full min-w-0 rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                    <option value="">All Categories</option>
                    @foreach (\App\Models\SupportTicket::CATEGORIES as $value => $label)
                        <option value="{{ $value }}" @selected(request('category') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="min-w-0 lg:col-span-2">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Status</label>
                <select name="status" onchange="this.form.submit()"
                    class="w-full min-w-0 rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                    <option value="">All Statuses</option>
                    @foreach (\App\Models\SupportTicket::STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="min-w-0 lg:col-span-2">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Date Range</label>
                <select name="date_range" onchange="this.form.submit()"
                    class="w-full min-w-0 rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                    <option value="">All Time</option>
                    <option value="last_7_days" @selected(request('date_range') === 'last_7_days')>Last 7 Days</option>
                    <option value="last_30_days" @selected(request('date_range') === 'last_30_days')>Last 30 Days</option>
                    <option value="this_month" @selected(request('date_range') === 'this_month')>This Month</option>
                    <option value="last_month" @selected(request('date_range') === 'last_month')>Last Month</option>
                </select>
            </div>

            <div class="min-w-0 lg:col-span-2">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Product</label>
                <select name="product" onchange="this.form.submit()"
                    class="w-full min-w-0 rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                    <option value="">All Products</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" @selected(request('product') == $product->id)>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark">
                <x-icon name="search" class="w-4 h-4" />
                Search
            </button>

            @if (request()->anyFilled(['search', 'category', 'status', 'date_range', 'product']))
                <a href="{{ route('admin.support.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-primary/30 px-4 py-2 text-sm font-medium text-primary hover:bg-primary-light">
                    <x-icon name="refresh-cw" class="w-4 h-4" />
                    Reset Filters
                </a>
            @endif
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-app-border bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-app-border bg-surface-alt text-xs font-medium uppercase tracking-wide text-secondary">
                    <tr>
                        <th scope="col" class="px-6 py-4">Ticket</th>
                        <th scope="col" class="px-6 py-4">Client / Project</th>
                        <th scope="col" class="px-6 py-4">Product</th>
                        <th scope="col" class="px-6 py-4">Category</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                        <th scope="col" class="px-6 py-4">Submitted</th>
                        <th scope="col" class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-app-border bg-white">
                    @forelse ($tickets as $ticket)
                        @php
                            $statusBadge = support_ticket_status_badge($ticket->status);
                            $categoryBadge = support_ticket_category_badge($ticket->category);
                            $firstMessage = $ticket->messages->first();
                            $statusIconColor = match ($ticket->status) {
                                'in_progress' => 'text-primary',
                                'resolved' => 'text-success',
                                'closed' => 'text-secondary-dark',
                                default => 'text-warning',
                            };
                        @endphp
                        <tr class="transition-colors hover:bg-surface-alt/60">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.support.show', $ticket) }}" class="font-medium text-secondary-dark hover:text-primary">
                                    {{ $ticket->ticket_no }}
                                </a>
                                @if ($firstMessage)
                                    <p class="mt-1 max-w-xs truncate text-xs text-secondary">{{ $firstMessage->message }}</p>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <span class="block font-medium text-secondary-dark">{{ $ticket->project->client->company_name }}</span>
                                <span class="block text-xs text-secondary">{{ $ticket->project->brand_name ?? $ticket->project->project_name }}</span>
                            </td>

                            <td class="px-6 py-4 font-medium text-secondary-dark">
                                {{ optional($ticket->project->product)->name ?? 'N/A' }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">
                                <x-badge :classes="$categoryBadge['classes']" class="whitespace-nowrap">{{ $categoryBadge['label'] }}</x-badge>
                            </td>

                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('admin.support.status.update', $ticket) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="relative inline-block">
                                        <span class="pointer-events-none absolute left-3 top-1/2 h-1.5 w-1.5 -translate-y-1/2 rounded-full bg-current {{ $statusIconColor }}"></span>
                                        <select name="status" onchange="this.form.submit()"
                                            class="appearance-none bg-none rounded-full border-0 py-1.5 pl-7 pr-7 text-xs font-medium focus:ring-2 focus:ring-primary {{ $statusBadge['classes'] }}">
                                            @foreach (\App\Models\SupportTicket::STATUSES as $value => $label)
                                                <option value="{{ $value }}" @selected($ticket->status === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <x-icon name="chevron-down"
                                            class="pointer-events-none absolute right-2.5 top-1/2 h-3 w-3 -translate-y-1/2 {{ $statusIconColor }}" />
                                    </div>
                                </form>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-secondary">
                                {{ $ticket->created_at->format('d M Y') }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.support.show', $ticket) }}" title="View Ticket"
                                    class="inline-flex items-center justify-center rounded-md border border-primary/30 bg-primary-light p-1.5 text-primary hover:border-primary/60 hover:bg-primary/20">
                                    <x-icon name="eye" class="w-3.5 h-3.5" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-secondary">
                                <div class="flex flex-col items-center justify-center">
                                    <x-icon name="life-buoy" class="mb-3 h-12 w-12 text-gray-300" />
                                    @if (request()->anyFilled(['search', 'category', 'status', 'date_range', 'product']))
                                        <p class="text-base font-medium text-secondary-dark">No support requests match these filters</p>
                                    @else
                                        <p class="text-base font-medium text-secondary-dark">No support requests yet</p>
                                        <p class="mt-1">New support tickets from clients will appear here.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tickets->hasPages())
            <div class="border-t border-app-border bg-surface-alt px-6 py-4">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
