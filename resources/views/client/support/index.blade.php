@php
    $productOptions = $projects
        ->groupBy('product_id')
        ->map(fn ($productProjects) => [
            'label' => $productProjects->first()->product->name,
            'projects' => $productProjects->map(fn ($project) => [
                'id' => $project->id,
                'label' => $project->brand_name ?? $project->project_name,
            ])->values(),
        ]);
@endphp

<x-client-layout title="Support">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">Support</h1>
            <p class="mt-1 text-sm text-secondary">Raise a support request for any of your brands and track our responses here</p>
        </div>
        @if ($projects->isNotEmpty())
            <x-primary-button type="button" class="shrink-0" x-data="" x-on:click.prevent="$dispatch('open-modal', 'new-support-ticket')">
                <x-icon name="plus" class="w-4 h-4" />
                New Request
            </x-primary-button>
        @endif
    </div>

    <form method="GET" action="{{ route('client.support.index') }}" class="mt-6 rounded-xl border border-app-border bg-white p-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-12">
            <div class="min-w-0 lg:col-span-5">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Search Ticket</label>
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary" />
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by ticket no..."
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
                    @foreach (\App\Models\SupportTicket::STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
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
                <a href="{{ route('client.support.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-primary/30 px-4 py-2 text-sm font-medium text-primary hover:bg-primary-light">
                    <x-icon name="refresh-cw" class="w-4 h-4" />
                    Reset Filters
                </a>
            </div>
        @endif
    </form>

    <div class="mt-6 overflow-hidden rounded-xl border border-app-border bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-app-border bg-surface-alt text-xs font-medium uppercase tracking-wide text-secondary">
                    <tr>
                        <th scope="col" class="px-6 py-4">Ticket</th>
                        <th scope="col" class="px-6 py-4">Brand / Project</th>
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
                        @endphp
                        <tr class="transition-colors hover:bg-surface-alt/60">
                            <td class="px-6 py-4">
                                <a href="{{ route('client.support.show', $ticket) }}" class="font-medium text-secondary-dark hover:text-primary">
                                    {{ $ticket->ticket_no }}
                                </a>
                                @if ($firstMessage)
                                    <p class="mt-1 max-w-xs truncate text-xs text-secondary">{{ $firstMessage->message }}</p>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-secondary-dark">
                                {{ $ticket->project->brand_name ?? $ticket->project->project_name }}
                            </td>

                            <td class="px-6 py-4 font-medium text-secondary-dark">
                                {{ optional($ticket->project->product)->name ?? 'N/A' }}
                            </td>

                            <td class="px-6 py-4">
                                <x-badge :classes="$categoryBadge['classes']" dot>{{ $categoryBadge['label'] }}</x-badge>
                            </td>

                            <td class="px-6 py-4">
                                <x-badge :classes="$statusBadge['classes']" dot>{{ $statusBadge['label'] }}</x-badge>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-secondary">
                                {{ $ticket->created_at->format('d M Y') }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('client.support.show', $ticket) }}" title="View Ticket"
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
                                    @if ($projects->isEmpty())
                                        <p class="text-base font-medium text-secondary-dark">You don't have any projects assigned yet</p>
                                    @elseif (request()->anyFilled(['search', 'product', 'status']))
                                        <p class="text-base font-medium text-secondary-dark">No support requests match these filters</p>
                                    @else
                                        <p class="text-base font-medium text-secondary-dark">You haven't raised any support requests yet</p>
                                        <p class="mt-1">Click "New Request" to get started.</p>
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

    @if ($projects->isNotEmpty())
        <x-modal name="new-support-ticket" :show="$errors->any()" focusable>
            <form method="POST" action="{{ route('client.support.store') }}" class="p-6" x-data="{ productId: '{{ old('product_id') }}', projects: @js($productOptions) }">
                @csrf

                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-secondary-dark">Raise a Support Request</h2>
                    <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <div class="mt-6 space-y-5">
                    <div>
                        <x-input-label value="Product *" class="text-xs uppercase tracking-wide" />
                        <select x-model="productId" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary" required>
                            <option value="" disabled selected>Select a product</option>
                            <template x-for="(group, productId) in projects" :key="productId">
                                <option :value="productId" x-text="group.label"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="support_project_id" value="Brand *" class="text-xs uppercase tracking-wide" />
                        <select id="support_project_id" name="project_id" x-bind:disabled="!productId"
                            class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary disabled:bg-surface-alt disabled:text-secondary" required>
                            <option value="" disabled selected>Select a brand</option>
                            <template x-for="project in (projects[productId]?.projects ?? [])" :key="project.id">
                                <option :value="project.id" x-text="project.label" :selected="project.id == {{ (int) old('project_id') }}"></option>
                            </template>
                        </select>
                        <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="support_category" value="What do you need help with? *" class="text-xs uppercase tracking-wide" />
                        <select id="support_category" name="category" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary" required>
                            <option value="" disabled selected>Select a category</option>
                            @foreach (\App\Models\SupportTicket::CATEGORIES as $value => $label)
                                <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="support_description" value="Describe your request *" class="text-xs uppercase tracking-wide" />
                        <textarea id="support_description" name="description" rows="4" placeholder="Tell us what's going on..."
                            class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary" required>{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                    <x-primary-button>Submit Request</x-primary-button>
                </div>
            </form>
        </x-modal>
    @endif
</x-client-layout>
