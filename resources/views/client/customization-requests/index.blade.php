<x-client-layout title="Customization">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">Customization Requests</h1>
            <p class="mt-1 text-sm text-secondary">Request custom features for any of your projects and track their status here</p>
        </div>
        @if ($projects->isNotEmpty())
            <x-primary-button type="button" class="shrink-0" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-customization-request')">
                <x-icon name="plus" class="w-4 h-4" />
                Add Request Customization
            </x-primary-button>
        @endif
    </div>

    <form method="GET" action="{{ route('client.customization-requests.index') }}" class="mt-6 rounded-xl border border-app-border bg-white p-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-12">
            <div class="min-w-0 lg:col-span-5">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Search Request</label>
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary" />
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by request title..."
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
                    @foreach (\App\Models\CustomizationRequest::STATUSES as $value)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ ucfirst($value) }}</option>
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
                <a href="{{ route('client.customization-requests.index') }}"
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
                        <th scope="col" class="px-6 py-4">Request</th>
                        <th scope="col" class="px-6 py-4">Brand / Project</th>
                        <th scope="col" class="px-6 py-4">Product</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                        <th scope="col" class="px-6 py-4">Submitted</th>
                        <th scope="col" class="px-6 py-4">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-app-border bg-white">
                    @forelse ($requests as $customizationRequest)
                        @php $badge = customization_status_badge($customizationRequest->status); @endphp
                        <tr class="align-top transition-colors hover:bg-surface-alt/60">
                            <td class="px-6 py-4">
                                <p class="font-medium text-secondary-dark">{{ $customizationRequest->title }}</p>
                                <p class="mt-1 max-w-xs truncate text-xs text-secondary" title="{{ $customizationRequest->description }}">
                                    {{ $customizationRequest->description }}
                                </p>
                            </td>

                            <td class="px-6 py-4 text-secondary-dark">
                                {{ $customizationRequest->project->brand_name ?? $customizationRequest->project->project_name }}
                            </td>

                            <td class="px-6 py-4 font-medium text-secondary-dark">
                                {{ optional($customizationRequest->project->product)->name ?? 'N/A' }}
                            </td>

                            <td class="px-6 py-4">
                                <x-badge :classes="$badge['classes']" dot>{{ $badge['label'] }}</x-badge>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-secondary">
                                {{ $customizationRequest->created_at->format('d M Y') }}
                            </td>

                            <td class="px-6 py-4">
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'review-{{ $customizationRequest->id }}')"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-primary/30 bg-primary-light px-3 py-1.5 text-xs font-medium text-primary hover:bg-primary/20">
                                    <x-icon name="message-square" class="w-3.5 h-3.5" />
                                    Review
                                </button>
                            </td>
                        </tr>

                        <x-modal :name="'review-' . $customizationRequest->id" maxWidth="lg">
                            <div class="p-6">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h2 class="text-lg font-semibold text-secondary-dark">{{ $customizationRequest->title }}</h2>
                                        <p class="mt-0.5 text-xs text-secondary">
                                            {{ $customizationRequest->project->brand_name ?? $customizationRequest->project->project_name }}
                                            &middot; {{ optional($customizationRequest->project->product)->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <button type="button" x-on:click="$dispatch('close')" class="shrink-0 text-secondary hover:text-secondary-dark">
                                        <x-icon name="x" class="w-5 h-5" />
                                    </button>
                                </div>

                                <div class="mt-5 max-h-80 space-y-4 overflow-y-auto pr-1">
                                    <!-- Your request -->
                                    <div class="flex items-start gap-2.5">
                                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-secondary-light text-xs font-semibold text-secondary-dark">
                                            {{ Str::substr($customizationRequest->createdBy->name, 0, 1) }}
                                        </span>
                                        <div class="max-w-[85%] rounded-2xl rounded-tl-sm bg-surface-alt px-4 py-2.5">
                                            <p class="whitespace-pre-line text-sm text-secondary-dark">{{ $customizationRequest->description }}</p>
                                            <p class="mt-1.5 text-[11px] text-secondary">
                                                {{ $customizationRequest->createdBy->name }} &middot; {{ $customizationRequest->created_at->format('d M Y, g:i A') }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Admin's response, if already reviewed -->
                                    @if ($customizationRequest->reviewed_at || $customizationRequest->admin_notes)
                                        @php $modalBadge = customization_status_badge($customizationRequest->status); @endphp
                                        <div class="flex items-start justify-end gap-2.5">
                                            <div class="max-w-[85%] rounded-2xl rounded-tr-sm bg-primary-light px-4 py-2.5">
                                                <x-badge :classes="$modalBadge['classes']" dot class="mb-1.5">{{ $modalBadge['label'] }}</x-badge>
                                                @if ($customizationRequest->admin_notes)
                                                    <p class="whitespace-pre-line text-sm text-secondary-dark">{{ $customizationRequest->admin_notes }}</p>
                                                @endif
                                                <p class="mt-1.5 text-[11px] text-secondary">
                                                    {{ optional($customizationRequest->reviewedBy)->name ?? 'Admin' }}
                                                    @if ($customizationRequest->reviewed_at)
                                                        &middot; {{ $customizationRequest->reviewed_at->format('d M Y, g:i A') }}
                                                    @endif
                                                </p>
                                            </div>
                                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-semibold text-white">
                                                {{ Str::substr(optional($customizationRequest->reviewedBy)->name ?? 'A', 0, 1) }}
                                            </span>
                                        </div>
                                    @else
                                        <p class="text-center text-xs text-secondary">Awaiting a response from the team.</p>
                                    @endif
                                </div>
                            </div>
                        </x-modal>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-secondary">
                                <div class="flex flex-col items-center justify-center">
                                    <x-icon name="settings" class="mb-3 h-12 w-12 text-gray-300" />
                                    @if ($projects->isEmpty())
                                        <p class="text-base font-medium text-secondary-dark">You don't have any projects assigned yet</p>
                                    @elseif (request()->anyFilled(['search', 'product', 'status']))
                                        <p class="text-base font-medium text-secondary-dark">No customization requests match these filters</p>
                                    @else
                                        <p class="text-base font-medium text-secondary-dark">You haven't submitted any customization requests yet</p>
                                        <p class="mt-1">Click "Add Request Customization" to get started.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($requests->hasPages())
            <div class="border-t border-app-border bg-surface-alt px-6 py-4">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    @if ($projects->isNotEmpty())
        <x-modal name="add-customization-request" :show="$errors->any()" focusable>
            <form method="POST" action="{{ route('client.customization-requests.store') }}" class="p-6">
                @csrf

                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-secondary-dark">Request a Customization</h2>
                    <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <div class="mt-6 space-y-5">
                    <div>
                        <x-input-label for="customization_project_id" value="Project *" class="text-xs uppercase tracking-wide" />
                        <select id="customization_project_id" name="project_id" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary" required>
                            <option value="" disabled selected>Select a project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" @selected(old('project_id') == $project->id)>
                                    {{ $project->project_name }} ({{ $project->product->name }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="customization_title" value="Title *" class="text-xs uppercase tracking-wide" />
                        <x-text-input id="customization_title" name="title" class="mt-1.5" :value="old('title')" placeholder="e.g. Add WhatsApp notifications" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="customization_description" value="What would you like customized? *" class="text-xs uppercase tracking-wide" />
                        <textarea id="customization_description" name="description" rows="4" placeholder="Describe the feature or change you need..."
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
