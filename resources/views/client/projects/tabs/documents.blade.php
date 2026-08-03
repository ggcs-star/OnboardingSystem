@php
    $allEntries = $project->documentEntries();
    $groupedEntries = $allEntries->groupBy('group_slug');
    $groupOptions = $allEntries->pluck('group_label', 'group_slug')->unique();
    $onboarding = $onboarding ?? false;
    $readOnly = $readOnly ?? false;
    [$mandatoryDone, $mandatoryTotal] = $project->mandatoryDocumentsProgress();
    $docsComplete = $mandatoryDone === $mandatoryTotal;
@endphp

<form method="POST" action="{{ route('client.projects.documents.bulk-update', $project) }}" enctype="multipart/form-data"
    x-data="{ search: '', group: '', status: '' }">
    @csrf
    @method('PATCH')
    @if ($onboarding)
        <input type="hidden" name="onboarding" value="1">
    @endif

    @if ($allEntries->isNotEmpty())
        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
            <div class="relative flex-1 sm:min-w-[220px]">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary" />
                <input type="text" x-model="search" placeholder="Search documents by name..."
                    class="w-full rounded-lg border-app-border pl-10 text-sm shadow-sm focus:border-primary focus:ring-primary">
            </div>
            <select x-model="group" class="rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                <option value="">All Groups</option>
                @foreach ($groupOptions as $slug => $label)
                    <option value="{{ $slug }}">{{ $label }}</option>
                @endforeach
            </select>
            <select x-model="status" class="rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                <option value="">All Status</option>
                @foreach (\App\Models\Project::DOCUMENT_STATUSES as $s)
                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button type="button" x-on:click="search = ''; group = ''; status = ''"
                class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-app-border px-4 py-2 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                <x-icon name="refresh-cw" class="w-3.5 h-3.5" />
                Reset Filters
            </button>
        </div>
    @endif

    <div class="mt-4 overflow-x-auto rounded-xl border border-app-border bg-white">
        <table class="min-w-full divide-y divide-app-border text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                    <th class="px-4 py-3">Document</th>
                    <th class="px-4 py-3">Group</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Value</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-app-border">
                @forelse ($groupedEntries as $groupSlug => $entries)
                    @foreach ($entries as $entry)
                        @php
                            $badge = document_value_status_badge($entry->status);
                            $isLocked = $readOnly || $entry->status === 'approved';
                            $isBlockingMandatory = $entry->group_mandatory && $entry->required && ! in_array($entry->status, ['submitted', 'approved'], true);
                            $fieldErrorKey = "fields.{$groupSlug}.{$entry->field_key}";
                        @endphp
                        <tr x-show="
                                (search === '' || @js(Str::lower($entry->label)).includes(search.toLowerCase())) &&
                                (group === '' || group === '{{ $groupSlug }}') &&
                                (status === '' || status === '{{ $entry->status }}')
                            ">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2 font-medium text-secondary-dark">
                                    <x-icon name="file-text" class="w-4 h-4 shrink-0 text-secondary/60" />
                                    <span>
                                        {{ $entry->label }}
                                        @if ($entry->required)
                                            <span class="text-danger">*</span>
                                        @endif
                                        @if ($isBlockingMandatory)
                                            <span class="ml-1 text-xs font-medium text-danger">(Mandatory)</span>
                                        @endif
                                    </span>
                                </div>
                                @if ($entry->status === 'rejected' && $entry->remarks)
                                    <p class="mt-1 rounded-lg bg-danger-light px-2 py-1 text-xs text-danger">{{ $entry->remarks }}</p>
                                @endif
                                <x-input-error :messages="$errors->get($fieldErrorKey)" class="mt-1" />
                            </td>
                            <td class="px-4 py-3 text-secondary">{{ $entry->group_label }}</td>
                            <td class="px-4 py-3"><x-badge classes="bg-secondary-light text-secondary-dark">{{ ucfirst($entry->type) }}</x-badge></td>
                            <td class="px-4 py-3"><x-badge :classes="$badge['classes']">{{ $badge['label'] }}</x-badge></td>
                            <td class="px-4 py-3 min-w-[220px]">
                                @php $previewModal = 'preview-' . $groupSlug . '-' . $entry->field_key; @endphp
                                @if ($isLocked)
                                    <div class="rounded-lg border border-app-border bg-surface-alt px-3 py-2 text-sm text-secondary-dark">
                                        @if ($entry->file && $entry->type === 'image')
                                            <button type="button" x-data="" x-on:click="$dispatch('open-modal', '{{ $previewModal }}')" class="flex items-center gap-2">
                                                <img src="{{ asset('storage/' . $entry->file) }}" alt="{{ $entry->label }}" class="h-8 w-8 shrink-0 rounded-md border border-app-border object-cover">
                                                <span class="text-primary hover:underline">View file</span>
                                            </button>
                                        @elseif ($entry->file)
                                            <a href="{{ asset('storage/' . $entry->file) }}" target="_blank" class="text-primary hover:underline">View file</a>
                                        @else
                                            {{ $entry->value }}
                                        @endif
                                    </div>
                                @elseif (in_array($entry->type, ['image', 'pdf'], true))
                                    <input type="file" name="files[{{ $groupSlug }}][{{ $entry->field_key }}]"
                                        accept="{{ $entry->type === 'image' ? 'image/*' : 'application/pdf' }}"
                                        @if ($isBlockingMandatory && ! $entry->file) required @endif
                                        class="w-full text-xs text-secondary file:mr-2 file:rounded-lg file:border-0 file:bg-primary-light file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-primary hover:file:bg-primary-light/70">
                                    @if ($entry->file && $entry->type === 'image')
                                        <button type="button" x-data="" x-on:click="$dispatch('open-modal', '{{ $previewModal }}')" class="mt-1.5 flex items-center gap-2">
                                            <img src="{{ asset('storage/' . $entry->file) }}" alt="{{ $entry->label }}" class="h-8 w-8 shrink-0 rounded-md border border-app-border object-cover">
                                            <span class="text-xs text-primary hover:underline">Current file</span>
                                        </button>
                                    @elseif ($entry->file)
                                        <a href="{{ asset('storage/' . $entry->file) }}" target="_blank" class="mt-1 block text-xs text-primary hover:underline">Current file</a>
                                    @endif
                                @else
                                    <input type="text" name="fields[{{ $groupSlug }}][{{ $entry->field_key }}]"
                                        value="{{ old($fieldErrorKey, $entry->value) }}" placeholder="{{ $entry->label }}"
                                        @if ($isBlockingMandatory) required @endif
                                        class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                                @endif

                                @if ($entry->file && $entry->type === 'image')
                                    <x-modal :name="$previewModal" maxWidth="lg">
                                        <div class="p-4">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-sm font-semibold text-secondary-dark">{{ $entry->label }}</h3>
                                                <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                                                    <x-icon name="x" class="w-5 h-5" />
                                                </button>
                                            </div>
                                            <img src="{{ asset('storage/' . $entry->file) }}" alt="{{ $entry->label }}" class="mt-3 max-h-[70vh] w-full rounded-lg object-contain">
                                        </div>
                                    </x-modal>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-sm text-secondary">No documents required for this project.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (! $readOnly && $groupedEntries->isNotEmpty())
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
