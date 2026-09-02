<x-admin-layout title="Documents">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">Documents</h1>
            <p class="mt-1 text-sm text-secondary">Guides, plans and presentations users can open in one click</p>
        </div>

        <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-document')">
            <x-icon name="plus" class="w-4 h-4" />
            Add Document
        </x-primary-button>
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
            <div class="flex flex-col overflow-hidden rounded-xl border border-app-border bg-white">
                @if ($document->thumbnailUrl())
                    <img src="{{ $document->thumbnailUrl() }}" alt="" class="aspect-video w-full object-cover">
                @else
                    <div class="flex aspect-video w-full items-center justify-center bg-primary-light text-primary">
                        <x-icon name="file-text" class="w-10 h-10" />
                    </div>
                @endif

                <div class="flex flex-1 flex-col p-5" x-data="{ expanded: false }">
                    <div class="flex items-start justify-between gap-2">
                        <p class="min-w-0 font-bold text-secondary-dark">{{ $document->title }}</p>
                        <div class="flex shrink-0 items-center gap-1">
                            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-document-{{ $document->id }}')" title="Edit"
                                class="inline-flex items-center justify-center rounded-md border border-warning/30 bg-warning-light p-1.5 text-warning hover:border-warning/60 hover:bg-warning/20">
                                <x-icon name="edit" class="w-3.5 h-3.5" />
                            </button>
                            <form method="POST" action="{{ route('admin.documents.destroy', $document) }}" onsubmit="return confirm('Delete this document?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Delete"
                                    class="inline-flex items-center justify-center rounded-md border border-danger/30 bg-danger-light p-1.5 text-danger hover:border-danger/60 hover:bg-danger/20">
                                    <x-icon name="trash" class="w-3.5 h-3.5" />
                                </button>
                            </form>
                        </div>
                    </div>

                    @if ($document->description)
                        <p class="mt-2 text-sm text-secondary" :class="expanded ? '' : 'line-clamp-3'">{{ $document->description }}</p>
                        <button type="button" x-on:click="expanded = !expanded" class="mt-1 self-start text-xs font-semibold text-primary hover:underline" x-text="expanded ? 'Read less' : 'Read more'"></button>
                    @endif

                    <a href="{{ $document->url }}" target="_blank" rel="noopener" class="mt-3 truncate text-xs text-primary hover:underline">{{ $document->url }}</a>

                    <form method="POST" action="{{ route('admin.documents.publish.toggle', $document) }}" class="mt-auto flex items-center justify-between border-t border-app-border pt-3">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="is_published" value="{{ $document->is_published ? '0' : '1' }}">
                        <span class="flex items-center gap-1.5 text-xs font-medium text-secondary">
                            <x-icon name="eye" class="w-3.5 h-3.5" />
                            {{ $document->is_published ? 'Visible to users' : 'Hidden' }}
                        </span>
                        <button type="submit">
                            <x-badge :classes="$document->is_published ? 'bg-success-light text-success' : 'bg-secondary-light text-secondary-dark'">
                                {{ $document->is_published ? 'Published' : 'Draft' }}
                            </x-badge>
                        </button>
                    </form>
                </div>
            </div>

            <x-modal name="edit-document-{{ $document->id }}" :show="false" max-width="lg">
                @include('admin.documents._form', ['document' => $document])
            </x-modal>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                @if (request('search'))
                    No documents match your search.
                @else
                    No documents yet. Click "Add Document" to create the first one.
                @endif
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $documents->links() }}
    </div>

    <x-modal name="add-document" :show="$errors->isNotEmpty()" focusable max-width="lg">
        @include('admin.documents._form', ['document' => null])
    </x-modal>
</x-admin-layout>
