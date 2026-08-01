@props(['stage', 'status' => 'active', 'templateDefined' => true, 'showLabels' => false])

@php
    $stages = array_keys(\App\Models\Project::STAGES);
    $currentIndex = array_search($stage, $stages, true) ?: 0;
    $dotSize = $showLabels ? 'h-9 w-9 text-xs' : 'h-6 w-6 text-[10px]';
    $iconSize = $showLabels ? 'w-4 h-4' : 'w-3 h-3';
@endphp

<div class="flex items-start">
    @foreach ($stages as $index => $key)
        @php
            $isDone = $index < $currentIndex;
            $isCurrent = $index === $currentIndex;
            $isBlocked = $isCurrent && $status === 'blocked';
            $isMissingTemplate = $index === 0 && ! $templateDefined;
        @endphp

        @if ($index > 0)
            <span class="mt-4 h-px w-6 shrink-0 {{ $isDone || $isCurrent ? 'bg-success' : 'bg-app-border' }} {{ $showLabels ? 'sm:w-10' : '' }}"></span>
        @endif

        <div class="flex flex-col items-center {{ $showLabels ? 'w-16' : '' }}">
            <span
                title="{{ $isMissingTemplate ? 'No document template defined for this product yet' : \App\Models\Project::STAGES[$key] }}"
                class="flex {{ $dotSize }} shrink-0 items-center justify-center rounded-full font-semibold ring-4
                    {{ $isMissingTemplate ? 'bg-warning text-white ring-warning-light' : ($isDone ? 'bg-success text-white ring-success-light' : ($isBlocked ? 'bg-danger text-white ring-danger-light' : ($isCurrent ? 'bg-primary text-white ring-primary-light' : 'bg-secondary-light text-secondary ring-transparent'))) }}"
            >
                @if ($isMissingTemplate)
                    <x-icon name="alert" class="{{ $iconSize }}" />
                @elseif ($isDone)
                    <x-icon name="check" class="{{ $iconSize }}" />
                @else
                    {{ Str::upper(Str::substr($key, 0, 1)) }}
                @endif
            </span>

            @if ($showLabels)
                <span class="mt-2 text-center text-[11px] font-medium leading-tight {{ $isCurrent ? 'text-secondary-dark' : 'text-secondary' }}">
                    {{ \App\Models\Project::STAGES[$key] }}
                </span>
            @endif
        </div>
    @endforeach
</div>
