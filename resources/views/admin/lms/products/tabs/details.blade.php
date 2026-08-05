<form method="POST" action="{{ route('admin.lms.products.update', $lmsProduct) }}" enctype="multipart/form-data"
    class="rounded-xl border border-app-border bg-white p-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div>
            <x-input-label for="name" value="Product Name *" class="text-xs uppercase tracking-wide" />
            <x-text-input id="name" name="name" class="mt-1.5" :value="old('name', $lmsProduct->name)" required />
            <p class="mt-1 text-xs text-secondary">
                Linked to purchased product:
                <span class="font-medium text-secondary-dark">{{ $lmsProduct->product->name ?? 'None — clients cannot access this yet' }}</span>
            </p>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="tagline" value="Short Description" class="text-xs uppercase tracking-wide" />
            <x-text-input id="tagline" name="tagline" class="mt-1.5" :value="old('tagline', $lmsProduct->tagline)" />
            <x-input-error :messages="$errors->get('tagline')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="active" value="Status" class="text-xs uppercase tracking-wide" />
            <select id="active" name="active"
                class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                <option value="1" @selected(old('active', $lmsProduct->active))>Active</option>
                <option value="0" @selected(! old('active', $lmsProduct->active))>Inactive</option>
            </select>
        </div>
    </div>

    <div class="mt-5">
        <x-input-label for="description" value="Description" class="text-xs uppercase tracking-wide" />
        <textarea id="description" name="description" rows="4"
            class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('description', $lmsProduct->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div class="mt-5">
        <x-input-label for="image" value="Product Image" class="text-xs uppercase tracking-wide" />
        @if ($lmsProduct->imageUrl())
            <img src="{{ $lmsProduct->imageUrl() }}" alt="{{ $lmsProduct->name }}" class="mt-2 h-16 w-16 rounded-lg border border-app-border object-cover">
        @endif
        <input id="image" name="image" type="file" accept="image/*"
            class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm file:mr-3 file:rounded-md file:border-0 file:bg-surface-alt file:px-3 file:py-1.5 file:text-sm">
        <x-input-error :messages="$errors->get('image')" class="mt-2" />
    </div>

    <div class="mt-8 flex justify-end gap-3">
        <a href="{{ route('admin.lms.products.index') }}">
            <x-secondary-button type="button">Cancel</x-secondary-button>
        </a>
        <x-primary-button>Save Changes</x-primary-button>
    </div>
</form>
