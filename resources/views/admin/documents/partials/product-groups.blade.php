@foreach ($products as $productGroup)
    <div class="rounded-lg border border-app-border" x-data="{ openProduct: {{ $loop->first ? 'true' : 'false' }} }">
        <button type="button" x-on:click="openProduct = !openProduct"
            class="flex w-full items-center justify-between gap-4 rounded-t-lg bg-surface-alt px-4 py-3 text-left">
            <div class="flex items-center gap-2">
                <x-icon name="box" class="w-4 h-4 text-secondary" />
                <span class="font-medium text-secondary-dark">{{ $productGroup->product->name }}</span>
                <span class="text-xs text-secondary">({{ $productGroup->brands->count() }} brand{{ $productGroup->brands->count() === 1 ? '' : 's' }})</span>
            </div>
            <div class="flex items-center gap-3">
                @if ($productGroup->pending > 0)
                    <x-badge classes="bg-warning-light text-warning">{{ $productGroup->pending }} pending</x-badge>
                @else
                    <x-badge classes="bg-success-light text-success">All clear</x-badge>
                @endif
                <x-icon name="chevron-down" class="w-4 h-4 shrink-0 text-secondary transition-transform" x-bind:class="openProduct && 'rotate-180'" />
            </div>
        </button>

        <div x-show="openProduct" x-cloak class="divide-y divide-app-border border-t border-app-border">
            @foreach ($productGroup->brands as $productRow)
                <div x-data="{ openBrand: {{ $loop->first ? 'true' : 'false' }} }">
                    <button type="button" x-on:click="openBrand = !openBrand"
                        class="flex w-full items-center justify-between gap-3 bg-surface-alt/40 px-4 py-2.5 text-left">
                        <span class="text-sm font-medium text-secondary-dark">{{ $productRow->project->brand_name ?? $productRow->project->project_name }}</span>
                        <div class="flex items-center gap-3">
                            @if ($productRow->pending > 0)
                                <x-badge classes="bg-warning-light text-warning">{{ $productRow->pending }} pending</x-badge>
                            @else
                                <x-badge classes="bg-success-light text-success">All clear</x-badge>
                            @endif
                            <x-icon name="chevron-down" class="w-3.5 h-3.5 shrink-0 text-secondary transition-transform" x-bind:class="openBrand && 'rotate-180'" />
                        </div>
                    </button>

                    <div x-show="openBrand" x-cloak class="divide-y divide-app-border border-t border-app-border">
                        @foreach ($productRow->groups as $groupRow)
                            <div class="p-4" x-data="{ openGroup: true }">
                                <button type="button" x-on:click="openGroup = !openGroup" class="flex w-full items-center justify-between gap-3 text-left">
                                    <span class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-secondary">
                                        {{ $groupRow->label }}
                                        @if ($groupRow->mandatory)
                                            <x-badge classes="bg-warning-light text-warning">Mandatory</x-badge>
                                        @endif
                                    </span>
                                    <x-icon name="chevron-down" class="w-3.5 h-3.5 shrink-0 text-secondary transition-transform" x-bind:class="openGroup && 'rotate-180'" />
                                </button>

                                <div x-show="openGroup" x-cloak class="mt-3 overflow-x-auto rounded-lg border border-app-border">
                                    <table class="min-w-full divide-y divide-app-border text-sm">
                                        <thead>
                                            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                                                <th class="px-4 py-2">Field</th>
                                                <th class="px-4 py-2">Submitted Value</th>
                                                <th class="px-4 py-2">Submitted</th>
                                                <th class="px-4 py-2">Status</th>
                                                <th class="px-4 py-2 text-right">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-app-border bg-white">
                                            @foreach ($groupRow->entries as $entry)
                                    @php
                                        $badge = document_value_status_badge($entry->status);
                                        $typeBadge = document_field_type_badge($entry->type);
                                        $modalName = 'review-' . $entry->project->id . '-' . $entry->group_slug . '-' . $entry->field_key;
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2 font-medium text-secondary-dark">
                                                <x-icon name="file-text" class="w-4 h-4 text-secondary/60" />
                                                {{ $entry->label }}
                                                @if ($entry->required)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-secondary">
                                            @if ($entry->file)
                                                <a href="{{ asset('storage/' . $entry->file) }}" target="_blank" class="text-primary hover:underline">View file</a>
                                            @elseif ($entry->value)
                                                {{ Str::limit($entry->value, 30) }}
                                            @else
                                                <span class="text-secondary/60">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-secondary">
                                            {{ $entry->submitted_at ? \Illuminate\Support\Carbon::parse($entry->submitted_at)->format('d-M-Y') : '—' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <x-badge :classes="$badge['classes']">{{ $badge['label'] }}</x-badge>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @if (in_array($entry->status, ['submitted', 'rejected'], true))
                                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', '{{ $modalName }}')"
                                                    class="inline-flex items-center gap-1.5 rounded-lg border border-app-border px-3 py-1.5 text-xs font-medium text-secondary-dark hover:bg-surface-alt">
                                                    <x-icon name="eye" class="w-3.5 h-3.5" />
                                                    Review
                                                </button>

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
                                            @else
                                                <span class="text-xs text-secondary/60">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endforeach
