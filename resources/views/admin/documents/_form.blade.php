@php
    $isEdit = (bool) $document;
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.documents.update', $document) : route('admin.documents.store') }}" class="p-6" enctype="multipart/form-data">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-secondary-dark">{{ $isEdit ? 'Edit Document' : 'Add Document' }}</h2>
        <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
            <x-icon name="x" class="w-5 h-5" />
        </button>
    </div>

    <div class="mt-6 space-y-5">
        <div>
            <x-input-label for="title-{{ $isEdit ? $document->id : 'new' }}" value="Title *" class="uppercase text-xs tracking-wide" />
            <x-text-input id="title-{{ $isEdit ? $document->id : 'new' }}" name="title" class="mt-1.5" :value="old('title', $isEdit ? $document->title : '')" placeholder="e.g. GG Prime Presentation" required autofocus />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="description-{{ $isEdit ? $document->id : 'new' }}" value="Description" class="uppercase text-xs tracking-wide" />
            <textarea id="description-{{ $isEdit ? $document->id : 'new' }}" name="description" rows="3" placeholder="Shown on the document card"
                class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('description', $isEdit ? $document->description : '') }}</textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="url-{{ $isEdit ? $document->id : 'new' }}" value="Link *" class="uppercase text-xs tracking-wide" />
            <x-text-input id="url-{{ $isEdit ? $document->id : 'new' }}" name="url" class="mt-1.5" :value="old('url', $isEdit ? $document->url : '')" placeholder="https://docs.google.com/..." required />
            <x-input-error :messages="$errors->get('url')" class="mt-2" />
            <p class="mt-1.5 text-xs text-secondary">Google Docs/Slides/Sheets links, or any PDF/website link — opens in a new tab.</p>
        </div>

        <div>
            <x-input-label for="thumbnail-{{ $isEdit ? $document->id : 'new' }}" value="Thumbnail (optional)" class="uppercase text-xs tracking-wide" />
            @if ($isEdit && $document->thumbnailUrl())
                <img src="{{ $document->thumbnailUrl() }}" alt="" class="mt-1.5 mb-2 h-20 w-full rounded-lg object-cover">
            @endif
            <input id="thumbnail-{{ $isEdit ? $document->id : 'new' }}" name="thumbnail" type="file" accept="image/*"
                class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm file:mr-3 file:rounded-md file:border-0 file:bg-surface-alt file:px-3 file:py-1.5 file:text-sm" />
            <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-3">
        <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
        <x-primary-button>{{ $isEdit ? 'Save Changes' : 'Add Document' }}</x-primary-button>
    </div>
</form>
