<x-admin-layout :title="$lmsProduct->name . ' — Preview'">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.lms.products.index') }}" class="hover:text-primary">Documentation</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <a href="{{ route('admin.lms.products.show', $lmsProduct) }}" class="hover:text-primary">{{ $lmsProduct->name }}</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">Preview</span>
    </nav>

    <div class="mt-3">
        <h1 class="text-xl font-semibold text-secondary-dark">{{ $lmsProduct->name }} — Documentation Preview</h1>
        <p class="mt-1 text-sm text-secondary">This is a read-only preview — exactly what an assigned client sees.</p>
    </div>

    <div class="mt-6 flex flex-col gap-6 lg:flex-row">
        <aside class="w-full shrink-0 lg:w-72">
            <nav class="space-y-1 rounded-xl border border-app-border bg-white p-3">
                @forelse ($lmsProduct->categories as $category)
                    @php
                        $categoryHasActive = $activeArticle && (
                            $category->articles->contains('id', $activeArticle->id)
                            || $category->subCategories->flatMap->articles->contains('id', $activeArticle->id)
                        );
                    @endphp
                    <div x-data="{ open: {{ $categoryHasActive || $loop->first ? 'true' : 'false' }} }" class="{{ !$loop->first ? 'mt-1 pt-1 border-t border-app-border' : '' }}">
                        <button type="button" x-on:click="open = !open"
                            class="flex w-full items-center gap-1.5 rounded-md px-2 py-2 text-left text-xs font-bold uppercase tracking-wide text-secondary-dark hover:bg-surface-alt">
                            <x-icon name="chevron-down" class="w-3.5 h-3.5 shrink-0 text-primary transition-transform duration-150" x-bind:class="!open && '-rotate-90'" />
                            {{ $category->name }}
                        </button>

                        <div x-show="open" x-cloak class="mt-0.5 space-y-0.5 pl-2">
                            @foreach ($category->articles as $article)
                                <a href="{{ route('admin.lms.products.preview.article', ['lmsProduct' => $lmsProduct, 'lmsArticle' => $article]) }}"
                                    class="flex items-center justify-between gap-2 rounded-md px-2 py-1.5 text-sm {{ $activeArticle && $activeArticle->id === $article->id ? 'bg-primary-light font-semibold text-primary' : 'text-secondary-dark hover:bg-surface-alt' }}">
                                    {{ $article->title }}
                                    @unless ($article->is_published)
                                        <x-badge classes="bg-secondary-light text-secondary shrink-0">Draft</x-badge>
                                    @endunless
                                </a>
                            @endforeach

                            @foreach ($category->subCategories as $subCategory)
                                @php
                                    $subHasActive = $activeArticle && $subCategory->articles->contains('id', $activeArticle->id);
                                @endphp
                                <div x-data="{ subOpen: {{ $subHasActive ? 'true' : 'true' }} }" class="mt-1.5">
                                    <button type="button" x-on:click="subOpen = !subOpen"
                                        class="flex w-full items-center gap-1.5 rounded-md px-2 py-1.5 text-left text-xs font-semibold text-primary hover:bg-primary-light/50">
                                        <x-icon name="chevron-down" class="w-3 h-3 shrink-0 transition-transform duration-150" x-bind:class="!subOpen && '-rotate-90'" />
                                        <x-icon name="folder" class="w-3.5 h-3.5 shrink-0" />
                                        {{ $subCategory->name }}
                                    </button>
                                    <div x-show="subOpen" x-cloak class="ml-3 space-y-0.5 border-l border-app-border pl-3 pt-0.5">
                                        @foreach ($subCategory->articles as $article)
                                            <a href="{{ route('admin.lms.products.preview.article', ['lmsProduct' => $lmsProduct, 'lmsArticle' => $article]) }}"
                                                class="flex items-center justify-between gap-2 rounded-md px-2 py-1.5 text-sm {{ $activeArticle && $activeArticle->id === $article->id ? 'bg-primary-light font-semibold text-primary' : 'text-secondary-dark hover:bg-surface-alt' }}">
                                                {{ $article->title }}
                                                @unless ($article->is_published)
                                                    <x-badge classes="bg-secondary-light text-secondary shrink-0">Draft</x-badge>
                                                @endunless
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="px-1 text-sm text-secondary">No categories yet.</p>
                @endforelse
            </nav>
        </aside>

        <div class="min-w-0 flex-1 rounded-xl border border-app-border bg-white p-6 lg:p-8">
            @if ($activeArticle)
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-secondary-dark">{{ $activeArticle->title }}</h2>
                        @if ($activeArticle->excerpt)
                            <p class="mt-2 text-sm text-secondary">{{ $activeArticle->excerpt }}</p>
                        @endif
                    </div>
                    <a href="{{ route('admin.lms.articles.edit', $activeArticle) }}" title="Edit this article"
                        class="inline-flex shrink-0 items-center gap-1.5 rounded-md border border-app-border px-2.5 py-1.5 text-xs font-medium text-secondary-dark hover:bg-surface-alt">
                        <x-icon name="edit" class="w-3.5 h-3.5" />
                        Edit
                    </a>
                </div>

                <div class="prose prose-sm sm:prose-base mt-6 max-w-none prose-pre:overflow-x-auto prose-img:rounded-lg">
                    {!! $activeArticle->content !!}
                </div>
            @else
                <div class="py-16 text-center text-secondary">
                    <x-icon name="file-text" class="mx-auto w-8 h-8 text-secondary/50" />
                    <p class="mt-3 text-sm">No articles yet — add one from "Categories & Articles".</p>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
