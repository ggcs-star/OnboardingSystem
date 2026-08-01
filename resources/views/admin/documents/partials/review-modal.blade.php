@props(['entry'])

@php
    $typeBadge = document_field_type_badge($entry->type);
    $modalName = 'review-' . $entry->project->id . '-' . $entry->group_slug . '-' . $entry->field_key;
@endphp

<x-modal :name="$modalName" focusable>
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-secondary-dark">Review Submission</h2>
                <p class="text-xs text-secondary">{{ $entry->project->project_name }} · {{ $entry->project->client->company_name }}</p>
            </div>
            <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs uppercase tracking-wide text-secondary">Field</p>
                <p class="mt-1 font-medium text-secondary-dark">{{ $entry->group_label }} · {{ $entry->label }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-secondary">Type</p>
                <p class="mt-1 font-medium text-secondary-dark">{{ $typeBadge['label'] }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-secondary">Product</p>
                <p class="mt-1 font-medium text-secondary-dark">{{ $entry->project->product->name }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-secondary">Submitted On</p>
                <p class="mt-1 font-medium text-secondary-dark">{{ $entry->submitted_at ? \Illuminate\Support\Carbon::parse($entry->submitted_at)->format('d-M-Y') : '—' }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.projects.documents.update', ['project' => $entry->project, 'group' => $entry->group_slug, 'field' => $entry->field_key]) }}" class="mt-5">
            @csrf
            @method('PATCH')

            <x-input-label value="Submitted Value" class="text-xs uppercase tracking-wide" />
            <div class="mt-1.5 rounded-lg border border-app-border bg-surface-alt px-3 py-2 text-sm text-secondary-dark">
                @if ($entry->file)
                    <a href="{{ asset('storage/' . $entry->file) }}" target="_blank" class="text-primary hover:underline">View uploaded file</a>
                @else
                    {{ $entry->value ?: '—' }}
                @endif
            </div>

            <div class="mt-6 flex justify-between">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <div class="flex gap-3">
                    <button type="submit" name="status" value="rejected" class="inline-flex items-center gap-2 rounded-lg bg-danger-light px-4 py-2.5 text-sm font-medium text-danger hover:opacity-80">
                        <x-icon name="x" class="w-4 h-4" />
                        Reject
                    </button>
                    <button type="submit" name="status" value="approved" class="inline-flex items-center gap-2 rounded-lg bg-success px-4 py-2.5 text-sm font-medium text-white hover:opacity-90">
                        <x-icon name="check" class="w-4 h-4" />
                        Approve
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-modal>
