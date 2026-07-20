<x-client-layout title="Training">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Training</h1>
        <p class="mt-1 text-sm text-secondary">Watch the training videos for every product you're onboarding</p>
    </div>

    <div class="mt-6 space-y-4">
        @forelse ($products as $product)
            @php
                $trainingIds = $product->training->pluck('id');
                $done = $client->trainingProgress->whereIn('training_id', $trainingIds)->where('completed', true)->count();
                $total = $trainingIds->count();
            @endphp
            <div class="rounded-xl border border-app-border bg-white" x-data="{ open: false }">
                <button type="button" @click="open = !open" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                            <x-icon name="video" class="w-4 h-4" />
                        </span>
                        <div>
                            <h2 class="font-semibold text-secondary-dark">{{ $product->name }}</h2>
                            @if ($product->tagline)
                                <p class="text-xs text-secondary">{{ $product->tagline }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-badge classes="bg-surface-alt text-secondary">{{ $done }}/{{ $total }} watched</x-badge>
                        <x-icon name="chevron-down" class="w-4 h-4 shrink-0 text-secondary transition" x-bind:class="open && '-rotate-180'" />
                    </div>
                </button>

                <div x-show="open" x-cloak class="space-y-3 border-t border-app-border p-5">
                    @forelse ($product->training as $video)
                        @php $progress = $client->trainingProgress->firstWhere('training_id', $video->id); @endphp
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
                        <div class="rounded-xl border border-dashed border-app-border bg-white p-6 text-center text-sm text-secondary">
                            No training videos for this product yet.
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                You don't have any products assigned yet.
            </div>
        @endforelse
    </div>
</x-client-layout>
