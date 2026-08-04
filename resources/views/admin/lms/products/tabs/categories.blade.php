@php
    $categories = $lmsProduct->categories;
@endphp

<div class="flex items-center justify-between">
    <div>
        <h2 class="text-lg font-semibold text-secondary-dark">Categories & Articles</h2>
        <p class="text-sm text-secondary">Build the doc tree clients will see — a category can have sub-categories, or hold articles directly.</p>
    </div>
    <a href="{{ route('admin.lms.articles.create', $lmsProduct) }}"
        class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-primary/30 bg-primary-light px-4 py-2.5 text-sm font-medium text-primary hover:border-primary/60 hover:bg-primary/20">
        <x-icon name="plus" class="w-4 h-4" />
        Add Article
    </a>
</div>

<div class="mt-6 space-y-4">
    @forelse ($categories as $category)
        <div x-data="{ open: true }" class="rounded-xl border border-app-border bg-white">
            <div class="flex items-center justify-between gap-3 px-4 py-3">
                <button type="button" x-on:click="open = !open" class="flex flex-1 items-center gap-2 text-left">
                    <x-icon name="chevron-down" class="w-4 h-4 text-secondary transition-transform" x-bind:class="!open && '-rotate-90'" />
                    <span class="font-semibold text-secondary-dark">{{ $category->name }}</span>
                    <span class="text-xs text-secondary">
                        {{ $category->subCategories->count() }} sub-categories · {{ $category->articles->count() }} articles
                    </span>
                </button>

                <div x-data="{ menuOpen: false }" class="relative" x-on:click.outside="menuOpen = false">
                    <button type="button" x-on:click.stop="menuOpen = !menuOpen"
                        class="rounded-md p-1.5 text-secondary hover:bg-surface-alt hover:text-secondary-dark" title="More options">
                        <x-icon name="more-vertical" class="w-4 h-4" />
                    </button>
                    <div x-show="menuOpen" x-cloak class="absolute right-0 z-10 mt-1 w-52 rounded-lg border border-app-border bg-white py-1 shadow-lg">
                        <button type="button" x-on:click="menuOpen = false; $dispatch('open-modal', 'edit-category-{{ $category->id }}')"
                            class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-secondary-dark hover:bg-surface-alt">
                            <x-icon name="edit" class="w-4 h-4" />
                            Edit Category
                        </button>
                        <form method="POST" action="{{ route('admin.lms.categories.destroy', $category) }}"
                            onsubmit="return confirm('Delete this category and everything under it?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-danger hover:bg-danger-light">
                                <x-icon name="trash" class="w-4 h-4" />
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div x-show="open" x-cloak class="divide-y divide-app-border border-t border-app-border">
                @if ($category->description)
                    <p class="px-4 py-3 text-sm text-secondary">{{ $category->description }}</p>
                @endif

                {{-- Articles directly under the category (no sub-category) --}}
                @forelse ($category->articles as $article)
                    <div class="flex items-center justify-between gap-3 px-4 py-2.5 pl-11">
                        <span class="flex items-center gap-2 text-sm text-secondary-dark">
                            <x-icon name="file-text" class="w-4 h-4 text-secondary" />
                            {{ $article->title }}
                            @unless ($article->is_published)
                                <x-badge classes="bg-secondary-light text-secondary">Draft</x-badge>
                            @endunless
                        </span>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.lms.articles.edit', $article) }}"
                                class="rounded-md p-1.5 text-secondary hover:bg-surface-alt hover:text-secondary-dark" title="Edit article">
                                <x-icon name="edit" class="w-3.5 h-3.5" />
                            </a>
                            <form method="POST" action="{{ route('admin.lms.articles.destroy', $article) }}" onsubmit="return confirm('Delete this article?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md p-1.5 text-secondary hover:bg-danger-light hover:text-danger" title="Delete article">
                                    <x-icon name="trash" class="w-3.5 h-3.5" />
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                @endforelse

                {{-- Sub-categories --}}
                @foreach ($category->subCategories as $subCategory)
                    <div x-data="{ subOpen: true }">
                        <div class="flex items-center justify-between gap-3 bg-surface-alt/60 px-4 py-2.5 pl-7">
                            <button type="button" x-on:click="subOpen = !subOpen" class="flex flex-1 items-center gap-2 text-left text-sm font-medium text-secondary-dark">
                                <x-icon name="chevron-down" class="w-3.5 h-3.5 text-secondary transition-transform" x-bind:class="!subOpen && '-rotate-90'" />
                                <x-icon name="folder" class="w-4 h-4 text-secondary" />
                                {{ $subCategory->name }}
                                <span class="text-xs font-normal text-secondary">{{ $subCategory->articles->count() }} articles</span>
                            </button>

                            <div x-data="{ menuOpen: false }" class="relative" x-on:click.outside="menuOpen = false">
                                <button type="button" x-on:click.stop="menuOpen = !menuOpen"
                                    class="rounded-md p-1.5 text-secondary hover:bg-white hover:text-secondary-dark" title="More options">
                                    <x-icon name="more-vertical" class="w-4 h-4" />
                                </button>
                                <div x-show="menuOpen" x-cloak class="absolute right-0 z-10 mt-1 w-52 rounded-lg border border-app-border bg-white py-1 shadow-lg">
                                    <button type="button" x-on:click="menuOpen = false; $dispatch('open-modal', 'edit-subcategory-{{ $subCategory->id }}')"
                                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-secondary-dark hover:bg-surface-alt">
                                        <x-icon name="edit" class="w-4 h-4" />
                                        Edit Sub-category
                                    </button>
                                    <form method="POST" action="{{ route('admin.lms.sub-categories.destroy', $subCategory) }}"
                                        onsubmit="return confirm('Delete this sub-category and its articles?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-danger hover:bg-danger-light">
                                            <x-icon name="trash" class="w-4 h-4" />
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div x-show="subOpen" x-cloak>
                            @forelse ($subCategory->articles as $article)
                                <div class="flex items-center justify-between gap-3 px-4 py-2.5 pl-16">
                                    <span class="flex items-center gap-2 text-sm text-secondary-dark">
                                        <x-icon name="file-text" class="w-4 h-4 text-secondary" />
                                        {{ $article->title }}
                                        @unless ($article->is_published)
                                            <x-badge classes="bg-secondary-light text-secondary">Draft</x-badge>
                                        @endunless
                                    </span>
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.lms.articles.edit', $article) }}"
                                            class="rounded-md p-1.5 text-secondary hover:bg-surface-alt hover:text-secondary-dark" title="Edit article">
                                            <x-icon name="edit" class="w-3.5 h-3.5" />
                                        </a>
                                        <form method="POST" action="{{ route('admin.lms.articles.destroy', $article) }}" onsubmit="return confirm('Delete this article?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-md p-1.5 text-secondary hover:bg-danger-light hover:text-danger" title="Delete article">
                                                <x-icon name="trash" class="w-3.5 h-3.5" />
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="px-4 py-3 pl-16 text-xs text-secondary">No articles yet.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <x-modal name="edit-category-{{ $category->id }}" maxWidth="md">
            <form method="POST" action="{{ route('admin.lms.categories.update', $category) }}" class="p-6">
                @csrf
                @method('PUT')
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-secondary-dark">Edit Category</h2>
                    <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>
                <div class="mt-6 space-y-5">
                    <div>
                        <x-input-label value="Name *" class="text-xs uppercase tracking-wide" />
                        <x-text-input name="name" class="mt-1.5" :value="$category->name" required />
                    </div>
                    <div>
                        <x-input-label value="Description" class="text-xs uppercase tracking-wide" />
                        <textarea name="description" rows="2" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ $category->description }}</textarea>
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-3">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                    <x-primary-button>Save</x-primary-button>
                </div>
            </form>
        </x-modal>

        @foreach ($category->subCategories as $subCategory)
            <x-modal name="edit-subcategory-{{ $subCategory->id }}" maxWidth="md">
                <form method="POST" action="{{ route('admin.lms.sub-categories.update', $subCategory) }}" class="p-6">
                    @csrf
                    @method('PUT')
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-secondary-dark">Edit Sub-category</h2>
                        <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                            <x-icon name="x" class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="mt-6 space-y-5">
                        <div>
                            <x-input-label value="Name *" class="text-xs uppercase tracking-wide" />
                            <x-text-input name="name" class="mt-1.5" :value="$subCategory->name" required />
                        </div>
                        <div>
                            <x-input-label value="Description" class="text-xs uppercase tracking-wide" />
                            <textarea name="description" rows="2" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ $subCategory->description }}</textarea>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-3">
                        <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            </x-modal>
        @endforeach
    @empty
        <div class="rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
            No categories yet. Click "Add Article" — you'll be able to create your first category right from there.
        </div>
    @endforelse
</div>
