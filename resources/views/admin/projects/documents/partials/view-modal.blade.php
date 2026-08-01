@props(['entry'])

@php
    $badge = document_value_status_badge($entry->status);
    $typeBadge = document_field_type_badge($entry->type);
    $modalName = 'view-' . $entry->project->id . '-' . $entry->group_slug . '-' . $entry->field_key;
@endphp

<x-modal :name="$modalName" focusable>
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-secondary-dark">{{ $entry->label }}</h2>
                <p class="text-xs text-secondary">{{ $entry->group_label }}</p>
            </div>
            <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs uppercase tracking-wide text-secondary">Type</p>
                <p class="mt-1 font-medium text-secondary-dark">{{ $typeBadge['label'] }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-secondary">Required</p>
                <p class="mt-1 font-medium text-secondary-dark">{{ $entry->required ? 'Yes' : 'No' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-secondary">Status</p>
                <p class="mt-1"><x-badge :classes="$badge['classes']">{{ $badge['label'] }}</x-badge></p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-secondary">Submitted On</p>
                <p class="mt-1 font-medium text-secondary-dark">{{ $entry->submitted_at ? \Illuminate\Support\Carbon::parse($entry->submitted_at)->format('d-M-Y g:i A') : '—' }}</p>
            </div>
            @if ($entry->approved_at)
                <div>
                    <p class="text-xs uppercase tracking-wide text-secondary">Reviewed On</p>
                    <p class="mt-1 font-medium text-secondary-dark">{{ \Illuminate\Support\Carbon::parse($entry->approved_at)->format('d-M-Y g:i A') }}</p>
                </div>
            @endif
        </div>

        <div class="mt-4">
            <p class="text-xs uppercase tracking-wide text-secondary">Submitted Value</p>
            <div class="mt-1.5 rounded-lg border border-app-border bg-surface-alt px-3 py-2 text-sm text-secondary-dark">
                @if ($entry->file)
                    <a href="{{ asset('storage/' . $entry->file) }}" target="_blank" class="text-primary hover:underline">View uploaded file</a>
                @else
                    {{ $entry->value ?: '—' }}
                @endif
            </div>
        </div>

        @if ($entry->remarks)
            <div class="mt-4">
                <p class="text-xs uppercase tracking-wide text-secondary">Remarks</p>
                <p class="mt-1 text-sm text-secondary-dark">{{ $entry->remarks }}</p>
            </div>
        @endif

        <div class="mt-6 flex justify-end">
            <x-secondary-button type="button" x-on:click="$dispatch('close')">Close</x-secondary-button>
        </div>
    </div>
</x-modal>
