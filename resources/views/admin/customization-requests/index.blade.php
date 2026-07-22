<x-admin-layout title="Customization Requests">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Customization Requests</h1>
        <p class="mt-1 text-sm text-secondary">See every customization request clients have submitted, grouped by product</p>
    </div>

    <div class="mt-6 space-y-6">
        @forelse ($products as $product)
            @php $pendingCount = $product->customizationRows->where('request.status', 'pending')->count(); @endphp
            <div class="overflow-hidden rounded-xl border border-app-border bg-white">
                <div class="flex items-center justify-between gap-3 border-b border-app-border bg-surface-alt px-5 py-3">
                    <div class="flex items-center gap-2">
                        <x-badge classes="bg-white text-secondary-dark">{{ $product->name }}</x-badge>
                        <span class="text-xs text-secondary">{{ $product->customizationRows->count() }} requests</span>
                    </div>
                    @if ($pendingCount > 0)
                        <x-badge classes="bg-warning-light text-warning">{{ $pendingCount }} pending review</x-badge>
                    @endif
                </div>

                <div class="divide-y divide-app-border">
                    @foreach ($product->customizationRows as $row)
                        @php $badge = customization_status_badge($row->request->status); @endphp
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-medium text-secondary-dark">{{ $row->request->title }}</p>
                                    <p class="text-xs text-secondary">{{ $row->client->company_name }} · {{ $row->project->project_name }}</p>
                                    <p class="mt-2 text-sm text-secondary">{{ $row->request->description }}</p>
                                    <p class="mt-2 text-xs text-secondary/70">
                                        Requested by {{ $row->request->createdBy->name }} on {{ $row->request->created_at->format('d-M-Y') }}
                                        @if ($row->request->reviewed_at)
                                            · Reviewed by {{ $row->request->reviewedBy?->name ?? '—' }} on {{ $row->request->reviewed_at->format('d-M-Y') }}
                                        @endif
                                    </p>
                                </div>
                                <x-badge :classes="$badge['classes']" class="shrink-0">{{ $badge['label'] }}</x-badge>
                            </div>

                            <form method="POST" action="{{ route('admin.projects.customization-requests.update', ['project' => $row->project, 'customizationRequest' => $row->request]) }}" class="mt-4 space-y-3">
                                @csrf
                                @method('PATCH')

                                <textarea name="admin_notes" rows="2" placeholder="Let the client know what's possible and what isn't..."
                                    class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('admin_notes', $row->request->admin_notes) }}</textarea>

                                <div class="flex flex-wrap gap-2">
                                    <button type="submit" name="status" value="approved" class="rounded-lg bg-success-light px-3 py-1.5 text-xs font-medium text-success hover:opacity-80">Approve</button>
                                    <button type="submit" name="status" value="partial" class="rounded-lg bg-primary-light px-3 py-1.5 text-xs font-medium text-primary hover:opacity-80">Partially Approve</button>
                                    <button type="submit" name="status" value="rejected" class="rounded-lg bg-danger-light px-3 py-1.5 text-xs font-medium text-danger hover:opacity-80">Reject</button>
                                </div>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                No customization requests yet.
            </div>
        @endforelse
    </div>
</x-admin-layout>
