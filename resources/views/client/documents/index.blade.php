<x-client-layout title="Documents">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">Documents</h1>
            <p class="mt-1 text-sm text-secondary">Guides, plans and presentations — click any card to open it</p>
        </div>
    </div>

    <form method="GET" class="mt-6 w-full sm:max-w-sm">
        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-secondary" />
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search documents..."
                class="w-full rounded-xl border-app-border py-3 pl-11 text-sm shadow-sm focus:border-primary focus:ring-primary">
        </div>
    </form>

    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($documents as $document)
            <a href="{{ $document->url }}" target="_blank" rel="noopener"
                class="flex flex-col overflow-hidden rounded-xl border border-app-border bg-white hover:border-primary/40 hover:shadow-sm">
                @if ($document->thumbnailUrl())
                    <img src="{{ $document->thumbnailUrl() }}" alt="" class="aspect-video w-full object-cover">
                @else
                    <div class="flex aspect-video w-full items-center justify-center bg-primary-light text-primary">
                        <x-icon name="file-text" class="w-10 h-10" />
                    </div>
                @endif

                <div class="flex flex-1 flex-col p-5" x-data="{ expanded: false }">
                    <p class="font-bold text-secondary-dark">{{ $document->title }}</p>
                    @if ($document->description)
                        <p class="mt-2 text-sm text-secondary" :class="expanded ? '' : 'line-clamp-3'">{{ $document->description }}</p>
                        <button type="button" x-on:click.prevent.stop="expanded = !expanded" class="mt-1 self-start text-xs font-semibold text-primary hover:underline" x-text="expanded ? 'Read less' : 'Read more'"></button>
                    @endif
                    <span class="mt-auto inline-flex items-center gap-1.5 pt-4 text-xs font-semibold text-primary">
                        <x-icon name="external-link" class="w-3.5 h-3.5" />
                        Open document
                    </span>
                </div>
            </a>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                @if (request('search'))
                    No documents match your search.
                @else
                    No documents have been added yet.
                @endif
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $documents->links() }}
    </div>
</x-client-layout>
