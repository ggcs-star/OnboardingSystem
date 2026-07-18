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
                [$trainingDone, $trainingTotal] = $project->trainingProgressCount();
            @endphp
            <div class="rounded-xl border border-app-border bg-white p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-primary-light text-primary">
                            <x-icon name="box" class="w-5 h-5" />
                        </span>
                        <div>
                            <h2 class="font-semibold text-secondary-dark">{{ $project->project_name }}</h2>
                            <p class="text-xs text-secondary">{{ $project->product->name }}</p>
                        </div>
                    </div>
                    <x-badge :classes="$stageBadge['classes']" dot>{{ $stageBadge['label'] }}</x-badge>
                </div>

                <div class="mt-5 space-y-4">
                    <x-charts.meter label="Documents submitted" :done="$docsDone" :total="$docsTotal" />
                    <x-charts.meter label="Training watched" :done="$trainingDone" :total="$trainingTotal" />
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

    @if ($projects->isNotEmpty())
        <div class="mt-6 overflow-x-auto rounded-xl border border-app-border bg-white">
            <table class="min-w-full divide-y divide-app-border text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                        <th class="px-6 py-3">Project</th>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Stage</th>
                        <th class="px-6 py-3">Docs</th>
                        <th class="px-6 py-3">Training</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-app-border">
                    @foreach ($projects as $project)
                        @php
                            $stageBadge = project_stage_badge($project->current_stage, $project->status);
                            [$docsDone, $docsTotal] = $project->documentsProgress();
                            [$trainingDone, $trainingTotal] = $project->trainingProgressCount();
                        @endphp
                        <tr>
                            <td class="px-6 py-3 font-medium text-secondary-dark">{{ $project->project_name }}</td>
                            <td class="px-6 py-3 text-secondary">{{ $project->product->name }}</td>
                            <td class="px-6 py-3">
                                <x-badge :classes="$stageBadge['classes']" dot>{{ $stageBadge['label'] }}</x-badge>
                            </td>
                            <td class="px-6 py-3 tabular-nums text-secondary">{{ $docsDone }}/{{ $docsTotal }}</td>
                            <td class="px-6 py-3 tabular-nums text-secondary">{{ $trainingDone }}/{{ $trainingTotal }}</td>
                            <td class="px-6 py-3 text-right">
                                <a href="{{ route('client.projects.show', $project) }}" class="text-xs font-medium text-primary hover:underline">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-client-layout>
