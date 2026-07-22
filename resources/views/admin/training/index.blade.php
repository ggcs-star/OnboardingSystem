<x-admin-layout title="Training">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">Training Progress</h1>
            <p class="mt-1 text-sm text-secondary">Manage training videos per product and see how far every client has gotten through them</p>
        </div>

        <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-training-video')">
            <x-icon name="plus" class="w-4 h-4" />
            Add Video
        </x-primary-button>
    </div>

    <div class="mt-6 space-y-3">
        @forelse ($products as $product)
            <div class="overflow-hidden rounded-xl border border-app-border bg-white" x-data="{ open: false, tab: 'videos' }">
                <button type="button" x-on:click="open = !open" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                            <x-icon name="box" class="w-5 h-5" />
                        </span>
                        <div>
                            <p class="font-semibold text-secondary-dark">{{ $product->name }}</p>
                            <p class="text-xs text-secondary">{{ $product->training->count() }} videos · {{ $product->clientRows->count() }} clients</p>
                        </div>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 shrink-0 text-secondary transition-transform" x-bind:class="open && 'rotate-180'" />
                </button>

                <div x-show="open" x-cloak class="border-t border-app-border">
                    <div class="flex gap-6 border-b border-app-border px-5">
                        <button type="button" x-on:click="tab = 'videos'"
                            class="border-b-2 px-1 py-3 text-sm font-medium transition"
                            x-bind:class="tab === 'videos' ? 'border-primary text-primary' : 'border-transparent text-secondary hover:text-secondary-dark'">
                            Videos
                        </button>
                        <button type="button" x-on:click="tab = 'clients'"
                            class="border-b-2 px-1 py-3 text-sm font-medium transition"
                            x-bind:class="tab === 'clients' ? 'border-primary text-primary' : 'border-transparent text-secondary hover:text-secondary-dark'">
                            Clients
                        </button>
                    </div>

                    <div x-show="tab === 'videos'" class="training-videos-list space-y-3 p-5" data-reorder-url="{{ route('admin.training-videos.reorder', $product) }}">
                        @forelse ($product->training as $video)
                            <div draggable="true" data-id="{{ $video->id }}" class="cursor-move rounded-lg border border-app-border p-4" x-data="{ playing: false }">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-3">
                                        <x-icon name="grip" class="mt-2 w-4 h-4 shrink-0 text-secondary/60" />
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                                            <x-icon name="video" class="w-4 h-4" />
                                        </span>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h3 class="font-medium text-secondary-dark">{{ $video->title }}</h3>
                                                @if ($video->duration)
                                                    <x-badge classes="bg-surface-alt text-secondary">{{ $video->duration }}</x-badge>
                                                @endif
                                            </div>
                                            @if ($video->description)
                                                <p class="mt-1 text-sm text-secondary">{{ $video->description }}</p>
                                            @endif
                                            <button type="button" x-on:click="playing = !playing" class="mt-1.5 inline-flex items-center gap-1 text-sm text-primary hover:underline">
                                                <span x-text="playing ? 'Hide preview' : 'Preview video'"></span>
                                            </button>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('admin.training-videos.destroy', $video) }}"
                                        onsubmit="return confirm('Remove this video?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-secondary hover:text-danger" title="Remove video">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
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
                            <div class="rounded-lg border border-dashed border-app-border p-6 text-center text-sm text-secondary">
                                No videos added for this product yet.
                            </div>
                        @endforelse
                    </div>

                    <div x-show="tab === 'clients'" x-cloak class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-app-border text-sm">
                            <thead>
                                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                                    <th class="px-5 py-3">Client</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3">Videos Watched</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-app-border">
                                @forelse ($product->clientRows as $row)
                                    @php
                                        $pct = $row->total > 0 ? round(($row->done / $row->total) * 100) : 0;
                                    @endphp
                                    <tr>
                                        <td class="px-5 py-3">
                                            <div class="font-medium text-secondary-dark">{{ $row->client->company_name }}</div>
                                        </td>
                                        <td class="px-5 py-3 text-secondary">
                                            @if ($row->onboarded)
                                                {{ $row->projectNames->implode(', ') }}
                                            @else
                                                <x-badge classes="bg-secondary-light text-secondary-dark">Not onboarded</x-badge>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="h-1.5 w-32 rounded-full bg-secondary-light">
                                                <div class="h-1.5 rounded-full {{ $pct === 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ $pct }}%"></div>
                                            </div>
                                            <div class="mt-1 text-xs text-secondary">{{ $pct }}% · {{ $row->done }}/{{ $row->total }} videos</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-5 py-8 text-center text-secondary">No clients yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                No products yet.
            </div>
        @endforelse
    </div>

    <x-modal name="add-training-video" :show="$errors->any()" focusable>
        <form method="POST" action="{{ route('admin.training-videos.store') }}" class="p-6"
            x-data="{
                videoUrl: @js(old('video_url', '')),
                embedUrl() {
                    if (!this.videoUrl) return null;
                    let m = this.videoUrl.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([a-zA-Z0-9_-]{11})/);
                    if (m) return 'https://www.youtube-nocookie.com/embed/' + m[1];
                    m = this.videoUrl.match(/vimeo\.com\/(\d+)/);
                    if (m) return 'https://player.vimeo.com/video/' + m[1];
                    return null;
                },
            }">
            @csrf

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-secondary-dark">Add Training Video</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <div class="mt-6 space-y-5">
                <div>
                    <x-input-label for="product_id" value="Product *" class="text-xs uppercase tracking-wide" />
                    <select id="product_id" name="product_id" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary" required>
                        <option value="">Select a product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="title" value="Title *" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="title" name="title" class="mt-1.5" placeholder="e.g. Getting Started — Login & Dashboard" :value="old('title')" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" value="Description" class="text-xs uppercase tracking-wide" />
                    <textarea id="description" name="description" rows="3" placeholder="What this video covers..."
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="video_url" value="Video URL *" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="video_url" name="video_url" class="mt-1.5" placeholder="YouTube or Vimeo link" x-model="videoUrl" required />
                    <x-input-error :messages="$errors->get('video_url')" class="mt-2" />

                    <div x-show="embedUrl()" x-cloak class="mt-3 aspect-video w-full overflow-hidden rounded-lg bg-black">
                        <iframe
                            x-bind:src="embedUrl()"
                            class="h-full w-full"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen
                            loading="lazy"
                        ></iframe>
                    </div>
                </div>

                <div>
                    <x-input-label for="duration" value="Duration" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="duration" name="duration" class="mt-1.5" placeholder="e.g. 8:24" :value="old('duration')" />
                    <x-input-error :messages="$errors->get('duration')" class="mt-2" />
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button>Add Video</x-primary-button>
            </div>
        </form>
    </x-modal>

    <script>
        (function () {
            document.querySelectorAll('.training-videos-list').forEach((list) => {
                let dragging = null;

                list.addEventListener('dragstart', (e) => {
                    dragging = e.target.closest('[data-id]');
                    e.dataTransfer.effectAllowed = 'move';
                });

                list.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    const target = e.target.closest('[data-id]');
                    if (!target || target === dragging) return;
                    const rect = target.getBoundingClientRect();
                    const next = (e.clientY - rect.top) / rect.height > 0.5;
                    list.insertBefore(dragging, next ? target.nextSibling : target);
                });

                list.addEventListener('drop', (e) => {
                    e.preventDefault();
                    const order = Array.from(list.querySelectorAll('[data-id]')).map((row) => row.dataset.id);
                    axios.post(list.dataset.reorderUrl, { order }).catch(() => window.location.reload());
                });
            });
        })();
    </script>
</x-admin-layout>
