@props(['stage', 'status' => 'active', 'templateDefined' => true])

@php
    $stages = array_keys(\App\Models\Project::STAGES);
    $currentIndex = array_search($stage, $stages, true) ?: 0;
@endphp

<div class="flex items-center">
    @foreach ($stages as $index => $key)
        @php
            $isDone = $index < $currentIndex;
            $isCurrent = $index === $currentIndex;
            $isBlocked = $isCurrent && $status === 'blocked';
            $isMissingTemplate = $index === 0 && ! $templateDefined;
        @endphp

        @if ($index > 0)
            <span class="h-px w-4 {{ $isDone || $isCurrent ? 'bg-success' : 'bg-app-border' }}"></span>
        @endif

        <span
            title="{{ $isMissingTemplate ? 'No document template defined for this product yet' : \App\Models\Project::STAGES[$key] }}"
            class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[10px] font-semibold
                {{ $isMissingTemplate ? 'bg-warning text-white' : ($isDone ? 'bg-success text-white' : ($isBlocked ? 'bg-danger text-white' : ($isCurrent ? 'bg-primary text-white' : 'bg-secondary-light text-secondary'))) }}"
        >
            @if ($isMissingTemplate)
                <x-icon name="alert" class="w-3 h-3" />
            @elseif ($isDone)
                <x-icon name="check" class="w-3 h-3" />
            @else
                {{ Str::upper(Str::substr($key, 0, 1)) }}
            @endif
        </span>
    @endforeach
</div>
