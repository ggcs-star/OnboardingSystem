@php
    $stageBadge = project_stage_badge($project->current_stage, $project->status);
    $completionPct = $total ? round($submittedCount / $total * 100) : 0;
@endphp

<x-admin-layout :title="$groupLabel">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.projects.index') }}" class="hover:text-primary">Ongoing Projects</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <a href="{{ route('admin.projects.show', $project) }}" class="hover:text-primary">{{ $project->brand_name ?? $project->project_name }}</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span>Documents</span>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">{{ $groupLabel }}</span>
    </nav>

    <div class="mt-3 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                <x-icon name="folder" class="w-5 h-5" />
            </span>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-semibold text-secondary-dark">{{ $groupLabel }}</h1>
                    @if ($groupMandatory)
                        <x-badge classes="bg-warning-light text-warning">Mandatory</x-badge>
                    @endif
                    @if ($readOnly)
                        <x-badge classes="bg-secondary-light text-secondary-dark">View Only</x-badge>
                    @endif
                </div>
                <p class="text-sm text-secondary">
                    {{ $project->brand_name ?? $project->project_name }} ({{ $project->product->category ?? $project->product->name }})
                </p>
            </div>
        </div>

        <a href="{{ route('admin.projects.show', $project) }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg border border-app-border bg-white px-4 py-2 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
            <x-icon name="chevron-left" class="w-4 h-4" />
            Back to Overview
        </a>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        <x-stat-card label="Total Documents" :value="$total" icon="file-text" />
        <x-stat-card label="Submitted" :value="$submittedCount" :hint="$completionPct . '% completed'" tint="success" icon="check" />
        <x-stat-card label="Pending" :value="$pendingCount" hint="remaining" tint="warning" icon="clock" />
        <x-stat-card label="Rejected" :value="$rejectedCount" hint="needs correction" tint="danger" icon="x" />
        <div class="rounded-xl border border-app-border bg-white p-5">
            <p class="text-xs font-medium uppercase tracking-wide text-secondary">Last Updated</p>
            <p class="mt-2 text-sm font-semibold text-secondary-dark">{{ $lastUpdated?->format('d-M-Y') ?? '—' }}</p>
            <p class="text-xs text-secondary">{{ $lastUpdated?->format('g:i A') }}</p>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-4">
        <div class="xl:col-span-3">
            <form method="GET" class="flex flex-col gap-3 sm:flex-row">
                <div class="relative flex-1">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary" />
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search documents by name..."
                        class="w-full rounded-lg border-app-border pl-10 text-sm shadow-sm focus:border-primary focus:ring-primary">
                </div>
                <select name="status" onchange="this.form.submit()" class="rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                    <option value="">All Status</option>
                    @foreach (\App\Models\Project::DOCUMENT_STATUSES as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <select name="type" onchange="this.form.submit()" class="rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                    <option value="">All Type</option>
                    @foreach (['text', 'number', 'image', 'pdf', 'key'] as $type)
                        <option value="{{ $type }}" @selected(request('type') === $type)>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
                @if ($readOnly)
                    <input type="hidden" name="readonly" value="1">
                @endif
                <button type="submit" class="hidden"></button>
                <a href="{{ route('admin.projects.documents.show', ['project' => $project, 'group' => $groupSlug, 'readonly' => $readOnly ? 1 : null]) }}"
                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-app-border px-4 py-2 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
                    <x-icon name="refresh-cw" class="w-3.5 h-3.5" />
                    Reset Filters
                </a>
            </form>

            <div class="mt-4 overflow-x-auto rounded-xl border border-app-border bg-white">
                <table class="min-w-full divide-y divide-app-border text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                            <th class="px-4 py-3">Document Name</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Required</th>
                            <th class="px-4 py-3">Submitted</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-app-border">
                        @forelse ($entries as $entry)
                            @php
                                $badge = document_value_status_badge($entry->status);
                                $typeBadge = document_field_type_badge($entry->type);
                                $viewModal = 'view-' . $entry->project->id . '-' . $entry->group_slug . '-' . $entry->field_key;
                                $editModal = 'edit-' . $entry->project->id . '-' . $entry->group_slug . '-' . $entry->field_key;
                            @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2 font-medium text-secondary-dark">
                                        <x-icon name="file-text" class="w-4 h-4 shrink-0 text-secondary/60" />
                                        <div>
                                            {{ $entry->label }}
                                            @if ($entry->required)
                                                <span class="text-danger">*</span>
                                            @endif
                                            @if ($entry->placeholder)
                                                <p class="text-xs font-normal text-secondary">{{ $entry->placeholder }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3"><x-badge :classes="$typeBadge['classes']">{{ $typeBadge['label'] }}</x-badge></td>
                                <td class="px-4 py-3 text-secondary">{{ $entry->required ? 'Yes' : 'No' }}</td>
                                <td class="px-4 py-3 text-secondary">
                                    {{ $entry->submitted_at ? \Illuminate\Support\Carbon::parse($entry->submitted_at)->format('d-M-Y g:i A') : '—' }}
                                </td>
                                <td class="px-4 py-3"><x-badge :classes="$badge['classes']">{{ $badge['label'] }}</x-badge></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" x-data="" x-on:click="$dispatch('open-modal', '{{ $viewModal }}')"
                                            class="rounded-lg p-1.5 text-secondary hover:bg-surface-alt hover:text-secondary-dark" title="View">
                                            <x-icon name="eye" class="w-4 h-4" />
                                        </button>
                                        @unless ($readOnly)
                                            <button type="button" x-data="" x-on:click="$dispatch('open-modal', '{{ $editModal }}')"
                                                class="rounded-lg p-1.5 text-secondary hover:bg-surface-alt hover:text-secondary-dark" title="Edit">
                                                <x-icon name="edit" class="w-4 h-4" />
                                            </button>
                                            @if ($entry->value || $entry->file)
                                                <form method="POST"
                                                    action="{{ route('admin.projects.documents.clear', ['project' => $entry->project, 'group' => $entry->group_slug, 'field' => $entry->field_key]) }}"
                                                    onsubmit="return confirm('Clear this submission? This cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="rounded-lg p-1.5 text-secondary hover:bg-danger-light hover:text-danger" title="Delete">
                                                        <x-icon name="trash" class="w-4 h-4" />
                                                    </button>
                                                </form>
                                            @else
                                                <span class="rounded-lg p-1.5 text-secondary/30" title="Nothing to delete">
                                                    <x-icon name="trash" class="w-4 h-4" />
                                                </span>
                                            @endif

                                            @if (in_array($entry->status, ['submitted', 'rejected'], true))
                                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'review-{{ $entry->project->id }}-{{ $entry->group_slug }}-{{ $entry->field_key }}')"
                                                    class="rounded-lg p-1.5 text-secondary hover:bg-surface-alt hover:text-secondary-dark" title="Review">
                                                    <x-icon name="check" class="w-4 h-4" />
                                                </button>
                                            @endif
                                        @endunless
                                    </div>
                                </td>
                            </tr>

                            @include('admin.projects.documents.partials.view-modal', ['entry' => $entry])
                            @unless ($readOnly)
                                @include('admin.projects.documents.partials.edit-modal', ['entry' => $entry])
                                @if (in_array($entry->status, ['submitted', 'rejected'], true))
                                    @include('admin.documents.partials.review-modal', ['entry' => $entry])
                                @endif
                            @endunless
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-sm text-secondary">No documents in this group.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $entries->links() }}
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-app-border bg-white p-6">
                <h2 class="text-xs font-semibold uppercase tracking-wide text-secondary">Document Group Summary</h2>
                <p class="mt-2 text-sm font-medium text-secondary-dark">{{ $groupLabel }}</p>
                <p class="mt-3 text-xs uppercase tracking-wide text-secondary">Completion Progress</p>
                <p class="mt-1 text-2xl font-semibold text-secondary-dark">{{ $completionPct }}%</p>
                <div class="mt-2 h-2.5 w-full overflow-hidden rounded-full bg-primary-light">
                    <div class="h-2.5 rounded-full {{ $completionPct >= 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ max($completionPct, $total ? 2 : 0) }}%"></div>
                </div>
                <p class="mt-2 text-xs text-secondary">{{ $submittedCount }} of {{ $total }} documents completed</p>
            </div>

            <div class="rounded-xl border border-app-border bg-white p-6">
                <h2 class="text-xs font-semibold uppercase tracking-wide text-secondary">Group Information</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-secondary">Product</dt>
                        <dd class="font-medium text-secondary-dark">{{ $project->product->name }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-secondary">Brand</dt>
                        <dd class="font-medium text-secondary-dark">{{ $project->brand_name ?? $project->project_name }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-secondary">Stage</dt>
                        <dd><x-badge :classes="$stageBadge['classes']">{{ $stageBadge['label'] }}</x-badge></dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-admin-layout>
