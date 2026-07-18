@props(['project', 'heading' => 'Client Timeline', 'footer' => null])

@php
    $stageBadge = project_stage_badge($project->current_stage, $project->status);
    [$docsDone, $docsTotal] = $project->documentsProgress();
    [$trainingDone, $trainingTotal] = $project->trainingProgressCount();
    $docsComplete = $docsTotal > 0 && $docsDone === $docsTotal;
    $trainingComplete = $trainingTotal > 0 && $trainingDone === $trainingTotal;
@endphp

<div {{ $attributes->merge(['class' => 'rounded-xl border border-app-border bg-white p-6']) }}>
    <p class="mb-4 text-xs font-semibold uppercase tracking-wide text-secondary">{{ $heading }}</p>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="flex items-center gap-3 rounded-lg border {{ $docsComplete ? 'border-success/30 bg-success-light' : 'border-app-border' }} p-4">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $docsComplete ? 'bg-success text-white' : 'bg-secondary-light text-secondary' }}">
                <x-icon :name="$docsComplete ? 'check' : 'file-text'" class="w-4 h-4" />
            </span>
            <div>
                <p class="text-sm font-medium text-secondary-dark">1. Submit Documents</p>
                <p class="text-xs text-secondary">{{ $docsDone }}/{{ $docsTotal }} fields submitted</p>
            </div>
        </div>
        <div class="flex items-center gap-3 rounded-lg border {{ $trainingComplete ? 'border-success/30 bg-success-light' : 'border-app-border' }} p-4">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $trainingComplete ? 'bg-success text-white' : 'bg-secondary-light text-secondary' }}">
                <x-icon :name="$trainingComplete ? 'check' : 'video'" class="w-4 h-4" />
            </span>
            <div>
                <p class="text-sm font-medium text-secondary-dark">2. Watch Training</p>
                <p class="text-xs text-secondary">{{ $trainingDone }}/{{ $trainingTotal }} videos watched</p>
            </div>
        </div>
    </div>
    @if ($footer)
        <p class="mt-4 text-xs text-secondary">{{ $footer }}</p>
    @else
        <p class="mt-4 text-xs text-secondary">
            This project is currently in <strong>{{ $stageBadge['label'] }}</strong>.
        </p>
    @endif
</div>
