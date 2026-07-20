<x-client-layout title="Training">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Training</h1>
        <p class="mt-1 text-sm text-secondary">Watch the training videos for every project you're onboarding</p>
    </div>

    <div class="mt-6 space-y-4">
        @forelse ($projects as $project)
            @php [$done, $total] = $project->trainingProgressCount(); @endphp
            <div class="rounded-xl border border-app-border bg-white" x-data="{ open: false }">
                <button type="button" @click="open = !open" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                            <x-icon name="video" class="w-4 h-4" />
                        </span>
                        <div>
                            <h2 class="font-semibold text-secondary-dark">{{ $project->project_name }}</h2>
                            <p class="text-xs text-secondary">{{ $project->product->name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-badge classes="bg-surface-alt text-secondary">{{ $done }}/{{ $total }} watched</x-badge>
                        <x-icon name="chevron-down" class="w-4 h-4 shrink-0 text-secondary transition" x-bind:class="open && '-rotate-180'" />
                    </div>
                </button>

                <div x-show="open" x-cloak class="space-y-3 border-t border-app-border p-5">
                    @forelse ($project->trainingProgress as $progress)
                        @php $video = $progress->training; @endphp
                        <div class="flex items-start justify-between gap-4 rounded-xl border border-app-border bg-white p-5">
                            <div>
                                <h3 class="font-medium text-secondary-dark">{{ $video->title }}</h3>
                                @if ($video->description)
                                    <p class="text-sm text-secondary">{{ $video->description }}</p>
                                @endif
                                <a href="{{ $video->video_url }}" target="_blank" rel="noopener" class="mt-1 inline-block text-sm text-primary hover:underline">
                                    Watch video @if ($video->duration) ({{ $video->duration }}) @endif
                                </a>
                            </div>

                            <form method="POST" action="{{ route('client.projects.training-progress.update', ['project' => $project, 'trainingProgress' => $progress]) }}" class="shrink-0">
                                @csrf
                                @method('PATCH')
                                @if ($progress->completed)
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-success-light px-3 py-1.5 text-xs font-medium text-success">
                                        <x-icon name="check" class="w-3.5 h-3.5" />
                                        Watched
                                    </button>
                                @else
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-app-border px-3 py-1.5 text-xs font-medium text-secondary-dark hover:bg-surface-alt">
                                        Mark as watched
                                    </button>
                                @endif
                            </form>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-app-border bg-white p-6 text-center text-sm text-secondary">
                            No training videos for this product yet.
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                You don't have any projects assigned yet.
            </div>
        @endforelse
    </div>
</x-client-layout>
