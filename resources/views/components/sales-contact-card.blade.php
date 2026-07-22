@props(['project'])

@if ($project->salesEmployee)
    <div {{ $attributes->merge(['class' => 'flex items-center gap-3 rounded-xl border border-app-border bg-white px-4 py-3']) }}>
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
            <x-icon name="briefcase" class="w-5 h-5" />
        </span>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-secondary">Your Sales Contact</p>
            <p class="text-sm font-medium text-secondary-dark">{{ $project->salesEmployee->name }}</p>
            <p class="text-sm text-secondary">
                {{ $project->salesEmployee->phone }}
                @if ($project->salesEmployee->email)
                    · {{ $project->salesEmployee->email }}
                @endif
            </p>
        </div>
    </div>
@endif
