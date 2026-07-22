<x-admin-layout title="Cheatsheets">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">Cheatsheets</h1>
            <p class="mt-1 text-sm text-secondary">Upload reference documents per product — every client on that product can view and download them</p>
        </div>

        <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-cheatsheet')">
            <x-icon name="plus" class="w-4 h-4" />
            Add Cheatsheet
        </x-primary-button>
    </div>

    <div class="mt-6 space-y-3">
        @forelse ($products as $product)
            <div class="overflow-hidden rounded-xl border border-app-border bg-white" x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                <button type="button" x-on:click="open = !open" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                            <x-icon name="box" class="w-5 h-5" />
                        </span>
                        <div>
                            <p class="font-semibold text-secondary-dark">{{ $product->name }}</p>
                            <p class="text-xs text-secondary">{{ $product->cheatsheets->count() }} document{{ $product->cheatsheets->count() === 1 ? '' : 's' }}</p>
                        </div>
                    </div>
                    <x-icon name="chevron-down" class="w-4 h-4 shrink-0 text-secondary transition-transform" x-bind:class="open && 'rotate-180'" />
                </button>

                <div x-show="open" x-cloak class="cheatsheets-list space-y-3 border-t border-app-border p-5" data-reorder-url="{{ route('admin.cheatsheets.reorder', $product) }}">
                    @forelse ($product->cheatsheets as $cheatsheet)
                        <div draggable="true" data-id="{{ $cheatsheet->id }}" class="flex cursor-move items-start justify-between gap-4 rounded-lg border border-app-border p-4">
                            <div class="flex items-start gap-3">
                                <x-icon name="grip" class="mt-2 w-4 h-4 shrink-0 text-secondary/60" />
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
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
                                    <div class="mt-1.5 flex items-center gap-3">
                                        <a href="{{ $cheatsheet->fileUrl() }}" target="_blank" class="inline-flex items-center gap-1 text-sm text-primary hover:underline">
                                            <x-icon name="eye" class="w-3.5 h-3.5" />
                                            View
                                        </a>
                                        <a href="{{ route('admin.cheatsheets.download', $cheatsheet) }}" class="inline-flex items-center gap-1 text-sm text-primary hover:underline">
                                            <x-icon name="download" class="w-3.5 h-3.5" />
                                            Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.cheatsheets.destroy', $cheatsheet) }}"
                                onsubmit="return confirm('Remove this cheatsheet?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-secondary hover:text-danger" title="Remove cheatsheet">
                                    <x-icon name="trash" class="w-4 h-4" />
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-app-border p-6 text-center text-sm text-secondary">
                            No cheatsheets added for this product yet.
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                No cheatsheets uploaded yet. Click "Add Cheatsheet" to get started.
            </div>
        @endforelse
    </div>

    <x-modal name="add-cheatsheet" :show="$errors->any()" focusable>
        <form method="POST" action="{{ route('admin.cheatsheets.store') }}" class="p-6" enctype="multipart/form-data">
            @csrf

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-secondary-dark">Add Cheatsheet</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <div class="mt-6 space-y-5">
                <div>
                    <x-input-label for="cheatsheet_product_id" value="Product *" class="text-xs uppercase tracking-wide" />
                    <select id="cheatsheet_product_id" name="product_id" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary" required>
                        <option value="">Select a product</option>
                        @foreach ($allProducts as $product)
                            <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="cheatsheet_title" value="Title *" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="cheatsheet_title" name="title" class="mt-1.5" placeholder="e.g. Quick Start Guide" :value="old('title')" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="cheatsheet_description" value="Description" class="text-xs uppercase tracking-wide" />
                    <textarea id="cheatsheet_description" name="description" rows="3" placeholder="What this document covers..."
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="cheatsheet_file" value="Document *" class="text-xs uppercase tracking-wide" />
                    <input id="cheatsheet_file" name="file" type="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx" required
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm text-secondary shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-primary-light file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-primary focus:border-primary focus:ring-primary">
                    <p class="mt-1 text-xs text-secondary">PDF, Word, Excel, or PowerPoint — up to 20MB.</p>
                    <x-input-error :messages="$errors->get('file')" class="mt-2" />
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button>Add Cheatsheet</x-primary-button>
            </div>
        </form>
    </x-modal>

    <script>
        (function () {
            document.querySelectorAll('.cheatsheets-list').forEach((list) => {
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
