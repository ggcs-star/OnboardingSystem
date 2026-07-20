<div class="space-y-3">
    @forelse ($project->product->training as $video)
        @php $progress = $project->trainingProgressFor($video); @endphp
        <div class="rounded-xl border border-app-border bg-white p-5" x-data="{ playing: false }">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="font-medium text-secondary-dark">{{ $video->title }}</h3>
                    @if ($video->description)
                        <p class="text-sm text-secondary">{{ $video->description }}</p>
                    @endif
                    <button type="button" @click="playing = !playing" class="mt-1 inline-flex items-center gap-1 text-sm text-primary hover:underline">
                        <span x-text="playing ? 'Hide video' : 'Watch video'"></span>
                        @if ($video->duration) ({{ $video->duration }}) @endif
                    </button>
                </div>

                <form method="POST" action="{{ route('client.training-progress.update', ['training' => $video]) }}" class="shrink-0">
                    @csrf
                    @method('PATCH')
                    @if ($progress?->completed)
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

            <div x-show="playing" x-cloak class="mt-4 aspect-video w-full overflow-hidden rounded-lg bg-black">
                <iframe
                    x-bind:src="playing ? @js($video->embedUrl()) : ''"
                    class="h-full w-full"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen
                    loading="lazy"
                ></iframe>
            </div>
        </div>
    @empty
        <div class="rounded-xl border border-dashed border-app-border bg-white p-8 text-center text-sm text-secondary">
            No training videos for this product yet.
        </div>
    @endforelse
</div>
