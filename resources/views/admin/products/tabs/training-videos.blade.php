<div class="flex items-center justify-between">
    <div>
        <h2 class="text-lg font-semibold text-secondary-dark">Training Videos</h2>
        <p class="text-sm text-secondary">Add video tutorials that clients watch after onboarding</p>
    </div>
    <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-training-video')">
        <x-icon name="plus" class="w-4 h-4" />
        Add Video
    </x-primary-button>
</div>

<div class="mt-6 space-y-3">
    @forelse ($product->training as $video)
        <div class="flex items-start justify-between gap-4 rounded-xl border border-app-border bg-white p-5">
            <div class="flex items-start gap-4">
                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary-light text-xs font-semibold text-primary">
                    {{ $loop->iteration }}
                </span>
                <div>
                    <h3 class="font-medium text-secondary-dark">{{ $video->title }}</h3>
                    @if ($video->description)
                        <p class="text-sm text-secondary">{{ $video->description }}</p>
                    @endif
                    <a href="{{ $video->video_url }}" target="_blank" rel="noopener" class="mt-1 inline-block text-sm text-primary hover:underline">
                        {{ $video->video_url }}
                    </a>
                </div>
            </div>

            <div class="flex shrink-0 items-center gap-3">
                @if ($video->duration)
                    <x-badge classes="bg-surface-alt text-secondary">{{ $video->duration }}</x-badge>
                @endif
                <form method="POST" action="{{ route('admin.products.training.destroy', ['product' => $product, 'training' => $video]) }}"
                    onsubmit="return confirm('Remove this video?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-secondary hover:text-danger">
                        <x-icon name="trash" class="w-4 h-4" />
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="rounded-xl border border-dashed border-app-border bg-white p-8 text-center text-sm text-secondary">
            No training videos yet.
        </div>
    @endforelse
</div>

<x-modal name="add-training-video" :show="$errors->any()" focusable>
    <form method="POST" action="{{ route('admin.products.training.store', $product) }}" class="p-6">
        @csrf

        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-secondary-dark">Add Training Video</h2>
            <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>

        <div class="mt-6 space-y-5">
            <div>
                <x-input-label for="title" value="Video Title *" class="text-xs uppercase tracking-wide" />
                <x-text-input id="title" name="title" class="mt-1.5" placeholder="e.g. Getting Started — Login & Dashboard" :value="old('title')" required />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="video_url" value="Video URL *" class="text-xs uppercase tracking-wide" />
                <x-text-input id="video_url" name="video_url" type="url" class="mt-1.5" placeholder="https://youtube.com/watch?v=..." :value="old('video_url')" required />
                <x-input-error :messages="$errors->get('video_url')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="duration" value="Duration" class="text-xs uppercase tracking-wide" />
                <x-text-input id="duration" name="duration" class="mt-1.5" placeholder="e.g. 8:24" :value="old('duration')" />
                <x-input-error :messages="$errors->get('duration')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="description" value="Description" class="text-xs uppercase tracking-wide" />
                <textarea id="description" name="description" rows="3" placeholder="Brief description of what this video covers"
                    class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-3">
            <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
            <x-primary-button>Add Video</x-primary-button>
        </div>
    </form>
</x-modal>
