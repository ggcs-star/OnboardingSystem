<div class="space-y-3">
    @forelse ($project->trainingProgress as $progress)
        @php $video = $progress->training; @endphp
        <div class="rounded-xl border border-app-border bg-white p-5">
            <div class="flex items-start justify-between gap-4">
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
        </div>
    @empty
        <div class="rounded-xl border border-dashed border-app-border bg-white p-8 text-center text-sm text-secondary">
            No training videos for this product yet.
        </div>
    @endforelse
</div>
