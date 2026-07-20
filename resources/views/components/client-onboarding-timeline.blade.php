@props(['project'])

@php
    [$docsDone, $docsTotal] = $project->documentsProgress();
    [$trainingDone, $trainingTotal] = $project->trainingProgressCount();
    $docsPct = $docsTotal > 0 ? round(($docsDone / $docsTotal) * 100) : 0;
    $trainingPct = $trainingTotal > 0 ? round(($trainingDone / $trainingTotal) * 100) : 0;
    $docsComplete = $docsTotal > 0 && $docsDone === $docsTotal;
    $trainingComplete = $trainingTotal > 0 && $trainingDone === $trainingTotal;
@endphp

<div class="flex items-center">
    <span
        title="Required Documents — {{ $docsPct }}% complete ({{ $docsDone }}/{{ $docsTotal }})"
        class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[10px] font-semibold
            {{ $docsComplete ? 'bg-success text-white' : 'bg-warning-light text-warning' }}"
    >
        @if ($docsComplete)
            <x-icon name="check" class="w-3 h-3" />
        @else
            {{ $docsPct }}
        @endif
    </span>

    <span class="h-px w-4 {{ $docsComplete ? 'bg-success' : 'bg-app-border' }}"></span>

    <span
        title="Training Progress — {{ $trainingPct }}% complete ({{ $trainingDone }}/{{ $trainingTotal }})"
        class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[10px] font-semibold
            {{ $trainingComplete ? 'bg-success text-white' : 'bg-primary-light text-primary' }}"
    >
        @if ($trainingComplete)
            <x-icon name="check" class="w-3 h-3" />
        @else
            {{ $trainingPct }}
        @endif
    </span>
</div>
