@props(['entry'])

@php
    $modalName = 'edit-' . $entry->project->id . '-' . $entry->group_slug . '-' . $entry->field_key;
@endphp

<x-modal :name="$modalName" focusable>
    <form method="POST"
        action="{{ route('admin.projects.documents.edit', ['project' => $entry->project, 'group' => $entry->group_slug, 'field' => $entry->field_key]) }}"
        enctype="multipart/form-data" class="p-6">
        @csrf
        @method('PUT')

        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-secondary-dark">Edit {{ $entry->label }}</h2>
                <p class="text-xs text-secondary">{{ $entry->group_label }}</p>
            </div>
            <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>

        <div class="mt-5">
            @if (in_array($entry->type, ['image', 'pdf'], true))
                <x-input-label value="Replace File" class="text-xs uppercase tracking-wide" />
                <input type="file" name="file" class="mt-1.5 block w-full text-sm text-secondary-dark file:mr-3 file:rounded-lg file:border-0 file:bg-primary-light file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-primary">
                @if ($entry->file)
                    <p class="mt-1 text-xs text-secondary">Current: <a href="{{ asset('storage/' . $entry->file) }}" target="_blank" class="text-primary hover:underline">view file</a></p>
                @endif
            @else
                <x-input-label value="Value" class="text-xs uppercase tracking-wide" />
                <x-text-input type="{{ $entry->type === 'number' ? 'number' : 'text' }}" name="value" value="{{ $entry->value }}" class="mt-1.5 w-full" />
            @endif
        </div>

        <div class="mt-4">
            <x-input-label value="Status" class="text-xs uppercase tracking-wide" />
            <select name="status" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                @foreach (\App\Models\Project::DOCUMENT_STATUSES as $status)
                    <option value="{{ $status }}" @selected($entry->status === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>

        <div class="mt-4">
            <x-input-label value="Remarks" class="text-xs uppercase tracking-wide" />
            <textarea name="remarks" rows="2" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ $entry->remarks }}</textarea>
        </div>

        <div class="mt-6 flex justify-between">
            <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
            <x-primary-button type="submit">Save Changes</x-primary-button>
        </div>
    </form>
</x-modal>
