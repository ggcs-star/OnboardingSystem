<div class="rounded-xl border border-app-border bg-white p-6">
    <h2 class="text-lg font-semibold text-secondary-dark">Product Information</h2>

    <form method="POST" action="{{ route('admin.products.update', $product) }}" class="mt-6 space-y-5" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <x-input-label for="name" value="Product Name" class="text-xs uppercase tracking-wide text-secondary" />
                <x-text-input id="name" name="name" class="mt-1.5" :value="old('name', $product->name)" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="category" value="Category" class="text-xs uppercase tracking-wide text-secondary" />
                <x-text-input id="category" name="category" class="mt-1.5" :value="old('category', $product->category)" />
                <x-input-error :messages="$errors->get('category')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="tagline" value="Tagline" class="text-xs uppercase tracking-wide text-secondary" />
            <x-text-input id="tagline" name="tagline" class="mt-1.5" :value="old('tagline', $product->tagline)" />
            <x-input-error :messages="$errors->get('tagline')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="introduction" value="Product Introduction / Description" class="text-xs uppercase tracking-wide text-secondary" />
            <textarea id="introduction" name="introduction" rows="5"
                class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('introduction', $product->introduction) }}</textarea>
            <x-input-error :messages="$errors->get('introduction')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="image" value="Product Icon" class="text-xs uppercase tracking-wide text-secondary" />
            <input id="image" type="file" name="image" accept="image/*"
                class="mt-1.5 block w-full text-sm text-secondary file:mr-4 file:rounded-lg file:border-0 file:bg-primary-light file:px-4 file:py-2 file:text-sm file:font-medium file:text-primary hover:file:bg-primary-light/70" />
            <x-input-error :messages="$errors->get('image')" class="mt-2" />
        </div>

        <div class="flex gap-3 pt-2">
            <x-primary-button>Save Changes</x-primary-button>
            <x-secondary-button type="reset">Discard</x-secondary-button>
        </div>
    </form>
</div>
