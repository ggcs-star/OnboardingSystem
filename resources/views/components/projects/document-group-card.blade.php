@props(['project', 'group'])

@php
    $pct = $group->total ? round($group->submitted / $group->total * 100) : 0;
    $complete = $group->total > 0 && $group->submitted === $group->total;
@endphp

<div class="rounded-lg border border-app-border p-4">
    <div class="flex items-center justify-between gap-2">
        <span class="flex items-center gap-2 font-medium text-secondary-dark">
            <x-icon name="folder" class="h-4 w-4 text-secondary" />
            {{ $group->label }}
        </span>
        @if ($group->mandatory)
            <x-badge classes="bg-warning-light text-warning">Mandatory</x-badge>
        @endif
    </div>

    <p class="mt-3 text-xs text-secondary">{{ $group->submitted }}/{{ $group->total }} Submitted</p>

    <div class="mt-1.5 h-2 w-full overflow-hidden rounded-full bg-primary-light">
        <div class="h-2 rounded-full {{ $complete ? 'bg-success' : 'bg-primary' }}" style="width: {{ max($pct, $group->total ? 2 : 0) }}%"></div>
    </div>

    <a href="{{ route('admin.projects.documents.show', ['project' => $project, 'group' => $group->slug, 'readonly' => 1]) }}"
        class="mt-3 inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-app-border px-3 py-1.5 text-xs font-medium text-secondary-dark hover:bg-surface-alt">
        View All ({{ $group->total }})
    </a>
</div>
