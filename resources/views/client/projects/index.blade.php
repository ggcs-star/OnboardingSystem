<x-client-layout title="Projects">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Projects</h1>
        <p class="mt-1 text-sm text-secondary">Every project you're onboarding with us</p>
    </div>

    <div class="mt-6 space-y-4">
        @forelse ($groupedProjects as $group)
            @php
                $product = $group['product'];
                $productProjects = $group['projects'];
            @endphp
            <div class="rounded-xl border border-app-border bg-white" x-data="{ open: true }">
                <button type="button" @click="open = !open" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                            <x-icon name="box" class="w-5 h-5" />
                        </span>
                        <div>
                            <h2 class="font-semibold text-secondary-dark">{{ $product->name }}</h2>
                            @if ($product->tagline)
                                <p class="text-xs text-secondary">{{ $product->tagline }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-badge classes="bg-surface-alt text-secondary">{{ $productProjects->count() }} project{{ $productProjects->count() === 1 ? '' : 's' }}</x-badge>
                        <x-icon name="chevron-down" class="w-4 h-4 shrink-0 text-secondary transition" x-bind:class="open && '-rotate-180'" />
                    </div>
                </button>

                <div x-show="open" x-cloak class="grid grid-cols-1 gap-4 border-t border-app-border p-5 lg:grid-cols-2">
                    @foreach ($productProjects as $project)
                        @php
                            $stageBadge = project_stage_badge($project->current_stage, $project->status);
                            [$docsDone, $docsTotal] = $project->documentsProgress();
                        @endphp
                        <div class="rounded-xl border border-app-border bg-surface-alt/40 p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-base font-semibold text-primary">{{ $project->brand_name ?? $project->project_name }}</p>
                                    <p class="text-xs text-secondary">{{ $product->name }}</p>
                                </div>
                                <x-badge :classes="$stageBadge['classes']" dot>{{ $stageBadge['label'] }}</x-badge>
                            </div>

                            <div class="mt-4">
                                <x-charts.meter label="Documents submitted" :done="$docsDone" :total="$docsTotal" />
                            </div>

                            <div class="mt-4 flex items-center gap-2">
                                <a href="{{ route('client.projects.show', ['project' => $project, 'tab' => 'documents']) }}" title="Upload Documents"
                                    class="inline-flex flex-1 items-center justify-center rounded-lg border border-app-border p-2.5 text-secondary hover:bg-surface-alt hover:text-primary">
                                    <x-icon name="file-text" class="w-4 h-4" />
                                </a>
                                <a href="{{ route('client.projects.show', ['project' => $project, 'tab' => 'customization']) }}" title="Customization"
                                    class="inline-flex flex-1 items-center justify-center rounded-lg border border-app-border p-2.5 text-secondary hover:bg-surface-alt hover:text-primary">
                                    <x-icon name="settings" class="w-4 h-4" />
                                </a>
                                <a href="{{ route('client.projects.show', ['project' => $project, 'tab' => 'sales-contact']) }}" title="Contact Sales Person"
                                    class="inline-flex flex-1 items-center justify-center rounded-lg border border-app-border p-2.5 text-secondary hover:bg-surface-alt hover:text-primary">
                                    <x-icon name="briefcase" class="w-4 h-4" />
                                </a>
                                <form method="POST" action="{{ route('client.projects.hold', $project) }}" class="flex-1"
                                    onsubmit="return confirm('{{ $project->status === 'blocked' ? 'Resume this project?' : 'Put this project on hold?' }}');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="{{ $project->status === 'blocked' ? 'Resume Project' : 'Hold Project' }}"
                                        class="inline-flex w-full items-center justify-center rounded-lg border border-app-border p-2.5 text-secondary hover:bg-surface-alt hover:text-primary">
                                        <x-icon :name="$project->status === 'blocked' ? 'play-circle' : 'pause-circle'" class="w-4 h-4" />
                                    </button>
                                </form>
                                <a href="{{ route('client.projects.show', $project) }}" title="View Project"
                                    class="inline-flex flex-1 items-center justify-center rounded-lg bg-primary p-2.5 text-white hover:bg-primary-dark">
                                    <x-icon name="eye" class="w-4 h-4" />
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                You don't have any projects assigned yet. Your onboarding team will notify you once one is created.
            </div>
        @endforelse
    </div>
</x-client-layout>
