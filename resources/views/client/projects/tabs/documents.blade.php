@php
    $groupedEntries = $project->documentEntries()->groupBy('group_slug');
    $onboarding = $onboarding ?? false;
    [$mandatoryDone, $mandatoryTotal] = $project->mandatoryDocumentsProgress();
    $docsComplete = $mandatoryDone === $mandatoryTotal;
@endphp

<form method="POST" action="{{ route('client.projects.documents.bulk-update', $project) }}" enctype="multipart/form-data">
    @csrf
    @method('PATCH')
    @if ($onboarding)
        <input type="hidden" name="onboarding" value="1">
    @endif

    <div class="space-y-8">
        @forelse ($groupedEntries as $groupSlug => $entries)
            <div>
                <h2 class="mb-4 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-secondary">
                    {{ $entries->first()->group_label }}
                    @if ($entries->first()->group_mandatory)
                        <x-badge classes="bg-warning-light text-warning">Required for onboarding</x-badge>
                    @endif
                </h2>

                <div class="space-y-4">
                    @foreach ($entries as $entry)
                        @php
                            $badge = document_value_status_badge($entry->status);
                            $isLocked = $entry->status === 'approved';
                            $isBlockingMandatory = $entry->group_mandatory && $entry->required && ! in_array($entry->status, ['submitted', 'approved'], true);
                            $fieldErrorKey = "fields.{$groupSlug}.{$entry->field_key}";
                        @endphp
                        <div class="rounded-xl border p-5 {{ $errors->has($fieldErrorKey) ? 'border-danger/40 bg-danger-light/20' : 'border-app-border bg-white' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-medium text-secondary-dark">{{ $entry->label }}</h3>
                                    @if ($entry->required)
                                        <span class="text-danger">*</span>
                                    @endif
                                    @if ($isBlockingMandatory)
                                        <span class="text-xs font-medium text-danger">(Mandatory)</span>
                                    @endif
                                </div>
                                <x-badge :classes="$badge['classes']">{{ $badge['label'] }}</x-badge>
                            </div>

                            @if ($entry->status === 'rejected' && $entry->remarks)
                                <p class="mt-2 rounded-lg bg-danger-light px-3 py-2 text-xs text-danger">{{ $entry->remarks }}</p>
                            @endif

                            @if ($isLocked)
                                <div class="mt-3 rounded-lg border border-app-border bg-surface-alt px-3 py-2 text-sm text-secondary-dark">
                                    @if ($entry->file)
                                        <a href="{{ asset('storage/' . $entry->file) }}" target="_blank" class="text-primary hover:underline">View submitted file</a>
                                    @else
                                        {{ $entry->value }}
                                    @endif
                                </div>
                            @elseif (in_array($entry->type, ['image', 'pdf'], true))
                                <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-center">
                                    <input type="file" name="files[{{ $groupSlug }}][{{ $entry->field_key }}]"
                                        accept="{{ $entry->type === 'image' ? 'image/*' : 'application/pdf' }}"
                                        @if ($isBlockingMandatory && ! $entry->file) required @endif
                                        class="flex-1 text-sm text-secondary file:mr-4 file:rounded-lg file:border-0 file:bg-primary-light file:px-4 file:py-2 file:text-sm file:font-medium file:text-primary hover:file:bg-primary-light/70">
                                </div>
                                @if ($entry->file)
                                    <a href="{{ asset('storage/' . $entry->file) }}" target="_blank" class="mt-2 inline-block text-xs text-primary hover:underline">View current file</a>
                                @endif
                            @else
                                <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-center">
                                    <input type="text" name="fields[{{ $groupSlug }}][{{ $entry->field_key }}]"
                                        value="{{ old($fieldErrorKey, $entry->value) }}" placeholder="{{ $entry->label }}"
                                        @if ($isBlockingMandatory) required @endif
                                        class="flex-1 rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                                </div>
                            @endif

                            <x-input-error :messages="$errors->get($fieldErrorKey)" class="mt-2" />
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-app-border bg-white p-8 text-center text-sm text-secondary">
                No documents required for this project.
            </div>
        @endforelse
    </div>

    @if ($groupedEntries->isNotEmpty())
        <div class="mt-6 flex items-center justify-end gap-3">
            @if ($mandatoryTotal > 0)
                <p class="text-xs text-secondary">Fields marked <span class="font-medium text-danger">(Mandatory)</span> must be filled to complete onboarding — everything else can be submitted later.</p>
            @endif
            <x-primary-button class="shrink-0">
                {{ $onboarding && $docsComplete ? 'Continue to Subscription' : 'Submit Documents' }}
            </x-primary-button>
        </div>
    @endif
</form>
