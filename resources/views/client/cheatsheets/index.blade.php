<x-client-layout title="Cheatsheets">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Cheatsheets</h1>
        <p class="mt-1 text-sm text-secondary">Reference documents shared by our team for every product you're using</p>
    </div>

    <div class="mt-6 space-y-4">
        @forelse ($products as $product)
            <div class="rounded-xl border border-app-border bg-white" x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                            <x-icon name="file-text" class="w-4 h-4" />
                        </span>
                        <div>
                            <h2 class="font-semibold text-secondary-dark">{{ $product->name }}</h2>
                            @if ($product->tagline)
                                <p class="text-xs text-secondary">{{ $product->tagline }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-badge classes="bg-surface-alt text-secondary">{{ $product->cheatsheets->count() }} document{{ $product->cheatsheets->count() === 1 ? '' : 's' }}</x-badge>
                        <x-icon name="chevron-down" class="w-4 h-4 shrink-0 text-secondary transition" x-bind:class="open && '-rotate-180'" />
                    </div>
                </button>

                <div x-show="open" x-cloak class="space-y-3 border-t border-app-border p-5">
                    @foreach ($product->cheatsheets as $cheatsheet)
                        <div class="flex items-start justify-between gap-4 rounded-xl border border-app-border bg-white p-5">
                            <div class="flex items-start gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                                    <x-icon name="file-text" class="w-4 h-4" />
                                </span>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-medium text-secondary-dark">{{ $cheatsheet->title }}</h3>
                                        <x-badge classes="bg-surface-alt text-secondary uppercase">{{ $cheatsheet->fileExtension() }}</x-badge>
                                    </div>
                                    @if ($cheatsheet->description)
                                        <p class="mt-1 text-sm text-secondary">{{ $cheatsheet->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-2">
                                <a href="{{ $cheatsheet->fileUrl() }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-app-border px-3 py-1.5 text-xs font-medium text-secondary-dark hover:bg-surface-alt">
                                    <x-icon name="eye" class="w-3.5 h-3.5" />
                                    View
                                </a>
                                <a href="{{ route('client.cheatsheets.download', $cheatsheet) }}"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-xs font-medium text-white hover:bg-primary-dark">
                                    <x-icon name="download" class="w-3.5 h-3.5" />
                                    Download
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                No cheatsheets have been shared yet.
            </div>
        @endforelse
    </div>
</x-client-layout>
