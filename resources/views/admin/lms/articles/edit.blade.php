<x-admin-layout :title="$article->title">
    @vite(['resources/js/lms-editor.js'])

    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.lms.products.index') }}" class="hover:text-primary">Documentation</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <a href="{{ route('admin.lms.products.show', $lmsProduct) }}" class="hover:text-primary">{{ $lmsProduct->name }}</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">{{ $article->title }}</span>
    </nav>

    <h1 class="mt-3 text-xl font-semibold text-secondary-dark">Edit Article</h1>

    <form method="POST" action="{{ route('admin.lms.articles.update', $article) }}" class="mt-6 rounded-xl border border-app-border bg-white p-6">
        @csrf
        @method('PUT')

        @include('admin.lms.articles._form')

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('admin.lms.products.show', $lmsProduct) }}">
                <x-secondary-button type="button">Cancel</x-secondary-button>
            </a>
            <x-primary-button>Save Changes</x-primary-button>
        </div>
    </form>
</x-admin-layout>
