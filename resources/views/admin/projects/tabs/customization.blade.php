<div class="space-y-4">
    @forelse ($project->customizationRequests as $customizationRequest)
        @php $badge = customization_status_badge($customizationRequest->status); @endphp
        <div class="rounded-xl border border-app-border bg-white p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h4 class="font-medium text-secondary-dark">{{ $customizationRequest->title }}</h4>
                    <p class="mt-1 text-sm text-secondary">{{ $customizationRequest->description }}</p>
                    <p class="mt-2 text-xs text-secondary/70">
                        Requested by {{ $customizationRequest->createdBy->name }} on {{ $customizationRequest->created_at->format('d-M-Y') }}
                    </p>
                </div>
                <x-badge :classes="$badge['classes']" class="shrink-0">{{ $badge['label'] }}</x-badge>
            </div>

            @if ($customizationRequest->reviewed_at)
                <p class="mt-2 text-xs text-secondary/70">
                    Reviewed by {{ $customizationRequest->reviewedBy?->name ?? '—' }} on {{ $customizationRequest->reviewed_at->format('d-M-Y') }}
                </p>
            @endif

            <form method="POST" action="{{ route('admin.projects.customization-requests.update', ['project' => $project, 'customizationRequest' => $customizationRequest]) }}" class="mt-4 space-y-3">
                @csrf
                @method('PATCH')

                <textarea name="admin_notes" rows="2" placeholder="Let the client know what's possible and what isn't..."
                    class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('admin_notes', $customizationRequest->admin_notes) }}</textarea>

                <div class="flex flex-wrap gap-2">
                    <button type="submit" name="status" value="approved" class="rounded-lg bg-success-light px-3 py-1.5 text-xs font-medium text-success hover:opacity-80">Approve</button>
                    <button type="submit" name="status" value="partial" class="rounded-lg bg-primary-light px-3 py-1.5 text-xs font-medium text-primary hover:opacity-80">Partially Approve</button>
                    <button type="submit" name="status" value="rejected" class="rounded-lg bg-danger-light px-3 py-1.5 text-xs font-medium text-danger hover:opacity-80">Reject</button>
                </div>
            </form>
        </div>
    @empty
        <div class="rounded-xl border border-dashed border-app-border bg-white p-8 text-center text-sm text-secondary">
            No customization requests from this client yet.
        </div>
    @endforelse
</div>
