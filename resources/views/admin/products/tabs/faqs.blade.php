<div class="flex items-center justify-between">
    <div>
        <h2 class="text-lg font-semibold text-secondary-dark">Frequently Asked Questions</h2>
        <p class="text-sm text-secondary">FAQs shown to all clients using this product</p>
    </div>
    <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-faq')">
        <x-icon name="plus" class="w-4 h-4" />
        Add FAQ
    </x-primary-button>
</div>

<div class="mt-6 space-y-3">
    @forelse ($product->faqs as $faq)
        <div class="flex items-start justify-between gap-4 rounded-xl border border-app-border bg-white p-5">
            <div class="flex items-start gap-3">
                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary-light text-xs font-semibold text-primary">
                    {{ $loop->iteration }}
                </span>
                <div>
                    <h3 class="font-medium text-secondary-dark">{{ $faq->question }}</h3>
                    <p class="mt-1 text-sm text-secondary">{{ $faq->answer }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.products.faqs.destroy', ['product' => $product, 'faq' => $faq]) }}"
                onsubmit="return confirm('Remove this FAQ?');" class="shrink-0">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-secondary hover:text-danger">
                    <x-icon name="trash" class="w-4 h-4" />
                </button>
            </form>
        </div>
    @empty
        <div class="rounded-xl border border-dashed border-app-border bg-white p-8 text-center text-sm text-secondary">
            No FAQs yet.
        </div>
    @endforelse
</div>

<x-modal name="add-faq" :show="$errors->any()" focusable>
    <form method="POST" action="{{ route('admin.products.faqs.store', $product) }}" class="p-6">
        @csrf

        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-secondary-dark">Add FAQ</h2>
            <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>

        <div class="mt-6 space-y-5">
            <div>
                <x-input-label for="question" value="Question *" class="text-xs uppercase tracking-wide" />
                <x-text-input id="question" name="question" class="mt-1.5" placeholder="e.g. How do I login?" :value="old('question')" required />
                <x-input-error :messages="$errors->get('question')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="answer" value="Answer *" class="text-xs uppercase tracking-wide" />
                <textarea id="answer" name="answer" rows="4" placeholder="Detailed answer..."
                    class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('answer') }}</textarea>
                <x-input-error :messages="$errors->get('answer')" class="mt-2" />
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-3">
            <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
            <x-primary-button>Add FAQ</x-primary-button>
        </div>
    </form>
</x-modal>
