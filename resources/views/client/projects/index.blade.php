<x-client-layout title="Projects">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Projects</h1>
        <p class="mt-1 text-sm text-secondary">Every project you're onboarding with us</p>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        @forelse ($projects as $project)
            @php
                $stageBadge = project_stage_badge($project->current_stage, $project->status);
                [$docsDone, $docsTotal] = $project->documentsProgress();
            @endphp
            <div class="rounded-xl border border-app-border bg-white p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-primary-light text-primary">
                            <x-icon name="box" class="w-5 h-5" />
                        </span>
                        <div>
                            <h2 class="font-semibold text-secondary-dark">{{ $project->product->name }}</h2>
                            <p class="text-xs text-secondary">{{ $project->brand_name ?? $project->project_name }}</p>
                        </div>
                    </div>
                    <x-badge :classes="$stageBadge['classes']" dot>{{ $stageBadge['label'] }}</x-badge>
                </div>

                <div class="mt-5 space-y-4">
                    <x-charts.meter label="Documents submitted" :done="$docsDone" :total="$docsTotal" />
                </div>

                <a href="{{ route('client.projects.show', $project) }}"
                    class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-dark">
                    <x-icon name="eye" class="w-4 h-4" />
                    View Project
                </a>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                You don't have any projects assigned yet. Your onboarding team will notify you once one is created.
            </div>
        @endforelse
    </div>
</x-client-layout>
