<x-client-layout :title="$lmsProduct->name . ' Documentation'">
    <div class="grid grid-cols-1 gap-x-6 gap-y-4 lg:grid-cols-12">
        <div class="flex items-start gap-4 lg:col-span-8">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-primary-light p-2">
                <img src="{{ optional($lmsProduct->product)->imageUrl() ?: asset('favicon.png') }}" alt="{{ $lmsProduct->name }}" class="h-full w-full object-contain">
            </span>
            <div>
                <h1 class="text-xl font-semibold text-secondary-dark">{{ $lmsProduct->name }}</h1>
                <p class="text-sm text-secondary">Documentation</p>
            </div>
        </div>

        <div x-data="{ switcherOpen: false }" class="relative lg:col-span-4">
            <button type="button" x-on:click="switcherOpen = !switcherOpen"
                class="flex w-full items-center gap-2 rounded-lg border border-app-border bg-white px-4 py-2.5 text-sm font-semibold text-secondary-dark hover:bg-surface-alt">
                <span class="flex h-5 w-5 shrink-0 items-center justify-center overflow-hidden rounded">
                    <img src="{{ optional($lmsProduct->product)->imageUrl() ?: asset('favicon.png') }}" alt="{{ $lmsProduct->name }}" class="h-full w-full object-contain">
                </span>
                <span class="flex-1 truncate text-left">{{ $lmsProduct->name }}</span>
                <x-icon name="chevron-down" class="w-4 h-4 shrink-0 text-secondary" />
            </button>

            <div x-show="switcherOpen" x-cloak x-on:click.outside="switcherOpen = false"
                class="absolute right-0 z-20 mt-2 w-full rounded-lg border border-app-border bg-white py-1 shadow-lg">
                @foreach ($assignedProducts as $product)
                    <a href="{{ route('client.lms.product', $product) }}"
                        class="flex items-center gap-2 px-4 py-2 text-sm {{ $product->id === $lmsProduct->id ? 'bg-primary-light text-primary font-medium' : 'text-secondary-dark hover:bg-surface-alt' }}">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center overflow-hidden rounded">
                            <img src="{{ optional($product->product)->imageUrl() ?: asset('favicon.png') }}" alt="{{ $product->name }}" class="h-full w-full object-contain">
                        </span>
                        {{ $product->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="min-w-0 rounded-xl border border-app-border bg-white p-6 lg:col-span-8 lg:p-8">
            @if ($activeArticle)
                <h1 class="text-2xl font-semibold text-secondary-dark">{{ $activeArticle->title }}</h1>
                @if ($activeArticle->excerpt)
                    <p class="mt-2 text-sm text-secondary">{{ $activeArticle->excerpt }}</p>
                @endif

                <div class="prose prose-sm sm:prose-base mt-6 max-w-none prose-pre:overflow-x-auto prose-img:rounded-lg">
                    {!! $activeArticle->content !!}
                </div>
            @else
                <div class="py-16 text-center text-secondary">
                    <x-icon name="file-text" class="mx-auto w-8 h-8 text-secondary/50" />
                    <p class="mt-3 text-sm">Select a document from the right to start reading.</p>
                </div>
            @endif
        </div>

        <aside class="lg:col-span-4">
          <nav class="sticky top-6 space-y-1 rounded-xl border border-app-border bg-white p-3">
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
                                <a href="{{ route('client.lms.article', ['lmsProduct' => $lmsProduct, 'lmsArticle' => $article]) }}"
                                    class="block rounded-md px-2 py-1.5 text-sm {{ $activeArticle && $activeArticle->id === $article->id ? 'bg-primary-light font-semibold text-primary' : 'text-secondary-dark hover:bg-surface-alt' }}">
                                    {{ $article->title }}
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
                                            <a href="{{ route('client.lms.article', ['lmsProduct' => $lmsProduct, 'lmsArticle' => $article]) }}"
                                                class="block rounded-md px-2 py-1.5 text-sm {{ $activeArticle && $activeArticle->id === $article->id ? 'bg-primary-light font-semibold text-primary' : 'text-secondary-dark hover:bg-surface-alt' }}">
                                                {{ $article->title }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="px-1 text-sm text-secondary">No documentation published yet.</p>
                @endforelse
            </nav>
        </aside>
    </div>
</x-client-layout>
