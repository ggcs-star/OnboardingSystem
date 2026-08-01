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

                                                @include('admin.documents.partials.review-modal', ['entry' => $entry])
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
