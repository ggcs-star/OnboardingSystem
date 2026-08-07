<x-admin-layout title="Customization Requests">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Customization Requests</h1>
        <p class="mt-1 text-sm text-secondary">See every customization request clients have submitted, across all products</p>
    </div>

    <form method="GET" action="{{ route('admin.customization-requests.index') }}" class="mt-6 rounded-xl border border-app-border bg-white p-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-12">
            <div class="min-w-0 lg:col-span-5">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Search Client / Request</label>
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary" />
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by client, project or request title..."
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
                <a href="{{ route('admin.customization-requests.index') }}"
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
                        <th scope="col" class="px-6 py-4">Client / Project</th>
                        <th scope="col" class="px-6 py-4">Product</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                        <th scope="col" class="px-6 py-4">Submitted</th>
                        <th scope="col" class="px-6 py-4">Review</th>
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

                            <td class="px-6 py-4">
                                <span class="block font-medium text-secondary-dark">{{ $customizationRequest->project->client->company_name }}</span>
                                <span class="block text-xs text-secondary">{{ $customizationRequest->project->project_name }}</span>
                            </td>

                            <td class="px-6 py-4 font-medium text-secondary-dark">
                                {{ optional($customizationRequest->project->product)->name ?? 'N/A' }}
                            </td>

                            <td class="px-6 py-4">
                                <x-badge :classes="$badge['classes']" dot>{{ $badge['label'] }}</x-badge>
                                <p class="mt-2 whitespace-nowrap text-xs text-secondary/70">
                                    By {{ $customizationRequest->createdBy->name }} on {{ $customizationRequest->created_at->format('d-M-Y') }}
                                    @if ($customizationRequest->reviewed_at)
                                        <br>Reviewed {{ $customizationRequest->reviewed_at->format('d-M-Y') }}
                                    @endif
                                </p>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-secondary">
                                {{ $customizationRequest->created_at->format('d M Y') }}
                            </td>

                            <td class="min-w-[280px] px-6 py-4">
                                <form method="POST" action="{{ route('admin.projects.customization-requests.update', ['project' => $customizationRequest->project, 'customizationRequest' => $customizationRequest]) }}" class="space-y-2">
                                    @csrf
                                    @method('PATCH')

                                    <textarea name="admin_notes" rows="2" placeholder="Let the client know what's possible and what isn't..."
                                        class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('admin_notes', $customizationRequest->admin_notes) }}</textarea>

                                    <div class="flex flex-wrap gap-2">
                                        <button type="submit" name="status" value="approved" class="rounded-lg bg-success-light px-3 py-1.5 text-xs font-medium text-success hover:opacity-80">Approve</button>
                                        <button type="submit" name="status" value="partial" class="rounded-lg bg-primary-light px-3 py-1.5 text-xs font-medium text-primary hover:opacity-80">Partial</button>
                                        <button type="submit" name="status" value="rejected" class="rounded-lg bg-danger-light px-3 py-1.5 text-xs font-medium text-danger hover:opacity-80">Reject</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-secondary">
                                <div class="flex flex-col items-center justify-center">
                                    <x-icon name="settings" class="mb-3 h-12 w-12 text-gray-300" />
                                    @if (request()->anyFilled(['search', 'product', 'status']))
                                        <p class="text-base font-medium text-secondary-dark">No customization requests match these filters</p>
                                    @else
                                        <p class="text-base font-medium text-secondary-dark">No customization requests yet</p>
                                        <p class="mt-1">New customization requests from clients will appear here.</p>
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
</x-admin-layout>
