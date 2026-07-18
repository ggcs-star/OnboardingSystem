<div class="space-y-4">
    @forelse ($project->documentValues as $value)
        @php
            $field = $value->documentField;
            $badge = document_value_status_badge($value->status);
            $isLocked = $value->status === 'approved';
        @endphp
        <div class="rounded-xl border border-app-border bg-white p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="font-medium text-secondary-dark">{{ $field->label }}</h3>
                    @if ($field->required)
                        <span class="text-danger">*</span>
                    @endif
                </div>
                <x-badge :classes="$badge['classes']">{{ $badge['label'] }}</x-badge>
            </div>

            @if ($field->placeholder)
                <p class="mt-1 text-xs text-secondary">{{ $field->placeholder }}</p>
            @endif

            @if ($value->status === 'rejected' && $value->remarks)
                <p class="mt-2 rounded-lg bg-danger-light px-3 py-2 text-xs text-danger">{{ $value->remarks }}</p>
            @endif

            <form method="POST" action="{{ route('client.projects.document-values.update', ['project' => $project, 'documentValue' => $value]) }}"
                class="mt-3" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                @if ($isLocked)
                    <div class="rounded-lg border border-app-border bg-surface-alt px-3 py-2 text-sm text-secondary-dark">
                        @if ($value->file)
                            <a href="{{ asset('storage/' . $value->file) }}" target="_blank" class="text-primary hover:underline">View submitted file</a>
                        @else
                            {{ $value->value }}
                        @endif
                    </div>
                @elseif (in_array($field->field_type, ['image', 'pdf'], true))
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <input type="file" name="file" accept="{{ $field->field_type === 'image' ? 'image/*' : 'application/pdf' }}"
                            class="flex-1 text-sm text-secondary file:mr-4 file:rounded-lg file:border-0 file:bg-primary-light file:px-4 file:py-2 file:text-sm file:font-medium file:text-primary hover:file:bg-primary-light/70">
                        <x-primary-button class="shrink-0">Submit</x-primary-button>
                    </div>
                    @if ($value->file)
                        <a href="{{ asset('storage/' . $value->file) }}" target="_blank" class="mt-2 inline-block text-xs text-primary hover:underline">View current file</a>
                    @endif
                @else
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <input type="text" name="value" value="{{ old('value', $value->value) }}" placeholder="{{ $field->placeholder }}"
                            class="flex-1 rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                        <x-primary-button class="shrink-0">Submit</x-primary-button>
                    </div>
                @endif
            </form>
        </div>
    @empty
        <div class="rounded-xl border border-dashed border-app-border bg-white p-8 text-center text-sm text-secondary">
            No documents required for this project.
        </div>
    @endforelse
</div>
