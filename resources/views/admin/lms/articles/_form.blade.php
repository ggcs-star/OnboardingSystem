@php
    $selectedCategory = old('lms_category_id', $article->lms_category_id ?? request('category') ?? optional($lmsProduct->categories->first())->id);
    $selectedSubCategory = old('lms_sub_category_id', $article->lms_sub_category_id ?? request('subcategory'));

    $categoriesForJs = $lmsProduct->categories->map(function ($category) {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'subCategories' => $category->subCategories->map(function ($sub) {
                return ['id' => $sub->id, 'name' => $sub->name];
            })->values(),
        ];
    });
@endphp

<div
    x-data="{
        categoryId: '{{ $selectedCategory }}',
        subCategoryId: '{{ $selectedSubCategory }}',
        categories: {{ \Illuminate\Support\Js::from($categoriesForJs) }},
        newlyAddedCategories: [],
        get currentSubCategories() {
            const category = this.categories.find(c => c.id === Number(this.categoryId));
            return category ? category.subCategories : [];
        },
        newSubCategoryName: '',
        newSubCategoryDescription: '',
        savingSubCategory: false,
        submitNewSubCategory() {
            if (! this.newSubCategoryName.trim()) return;
            this.savingSubCategory = true;

            window.axios.post('{{ url('/admin/lms/categories') }}/' + this.categoryId + '/sub-categories', {
                name: this.newSubCategoryName,
                description: this.newSubCategoryDescription,
            }, { headers: { Accept: 'application/json' } })
                .then(({ data }) => {
                    const category = this.categories.find(c => c.id === Number(this.categoryId));
                    if (category) category.subCategories.push({ id: data.id, name: data.name });
                    this.$nextTick(() => { this.subCategoryId = String(data.id); });
                    this.newSubCategoryName = '';
                    this.newSubCategoryDescription = '';
                    this.savingSubCategory = false;
                    this.$dispatch('close-modal', 'add-subcategory-inline');
                })
                .catch(() => {
                    this.savingSubCategory = false;
                    alert('Could not create the sub-category. Please try again.');
                });
        },
        newCategoryName: '',
        newCategoryDescription: '',
        savingCategory: false,
        submitNewCategory() {
            if (! this.newCategoryName.trim()) return;
            this.savingCategory = true;

            window.axios.post('{{ route('admin.lms.categories.store', $lmsProduct) }}', {
                name: this.newCategoryName,
                description: this.newCategoryDescription,
            }, { headers: { Accept: 'application/json' } })
                .then(({ data }) => {
                    this.categories.push({ id: data.id, name: data.name, subCategories: [] });
                    this.newlyAddedCategories.push({ id: data.id, name: data.name });
                    this.subCategoryId = '';
                    this.$nextTick(() => { this.categoryId = String(data.id); });
                    this.newCategoryName = '';
                    this.newCategoryDescription = '';
                    this.savingCategory = false;
                    this.$dispatch('close-modal', 'add-category-inline');
                })
                .catch(() => {
                    this.savingCategory = false;
                    alert('Could not create the category. Please try again.');
                });
        },
    }"
    x-init="
        $watch('subCategoryId', (value, oldValue) => {
            if (value === '__add__') {
                subCategoryId = (oldValue && oldValue !== '__add__') ? oldValue : '';
                $dispatch('open-modal', 'add-subcategory-inline');
            }
        });
        $watch('categoryId', (value, oldValue) => {
            if (value === '__add_category__') {
                categoryId = (oldValue && oldValue !== '__add_category__') ? oldValue : '';
                $dispatch('open-modal', 'add-category-inline');
            }
        });
    "
    class="space-y-6"
>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
            <x-input-label for="title" value="Article Title *" class="text-xs uppercase tracking-wide" />
            <x-text-input id="title" name="title" class="mt-1.5" :value="old('title', $article->title ?? '')" required autofocus />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="excerpt" value="Short Excerpt" class="text-xs uppercase tracking-wide" />
            <x-text-input id="excerpt" name="excerpt" class="mt-1.5" placeholder="Optional one-line summary" :value="old('excerpt', $article->excerpt ?? '')" />
            <x-input-error :messages="$errors->get('excerpt')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="lms_category_id" value="Category *" class="text-xs uppercase tracking-wide" />
            <select id="lms_category_id" name="lms_category_id" x-model="categoryId" x-on:change="subCategoryId = ''"
                class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                @foreach ($lmsProduct->categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
                <template x-for="category in newlyAddedCategories" :key="category.id">
                    <option :value="String(category.id)" x-text="category.name"></option>
                </template>
                <option value="__add_category__">+ Create New Category…</option>
            </select>
            @if ($lmsProduct->categories->isEmpty())
                <p class="mt-2 text-xs text-secondary" x-show="categories.length === 0">
                    <button type="button" x-on:click="$dispatch('open-modal', 'add-category-inline')" class="font-medium text-primary hover:underline">Create your first category</button>
                    to continue.
                </p>
            @endif
            <x-input-error :messages="$errors->get('lms_category_id')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="lms_sub_category_id" value="Sub-category" class="text-xs uppercase tracking-wide" />
            <select id="lms_sub_category_id" name="lms_sub_category_id" x-model="subCategoryId"
                class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                <option value="">— None (attach directly to category) —</option>
                <template x-for="sub in currentSubCategories" :key="sub.id">
                    <option :value="String(sub.id)" x-text="sub.name"></option>
                </template>
                <option value="__add__">+ Create New Sub-category</option>
            </select>
            <x-input-error :messages="$errors->get('lms_sub_category_id')" class="mt-2" />
        </div>
    </div>

    <x-modal name="add-subcategory-inline" maxWidth="md">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-secondary-dark">Add Sub-category</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <div class="mt-6 space-y-5">
                <div>
                    <x-input-label value="Name *" class="text-xs uppercase tracking-wide" />
                    <input type="text" x-model="newSubCategoryName" placeholder="e.g. Installation" x-on:keydown.enter.prevent="submitNewSubCategory()"
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <x-input-label value="Description" class="text-xs uppercase tracking-wide" />
                    <textarea x-model="newSubCategoryDescription" rows="2"
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"></textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button type="button" x-on:click="submitNewSubCategory()" x-bind:disabled="savingSubCategory || ! newSubCategoryName.trim()">
                    <span x-text="savingSubCategory ? 'Adding…' : 'Add Sub-category'"></span>
                </x-primary-button>
            </div>
        </div>
    </x-modal>

    <x-modal name="add-category-inline" maxWidth="md">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-secondary-dark">Add Category</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <div class="mt-6 space-y-5">
                <div>
                    <x-input-label value="Name *" class="text-xs uppercase tracking-wide" />
                    <input type="text" x-model="newCategoryName" placeholder="e.g. Getting Started" x-on:keydown.enter.prevent="submitNewCategory()"
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <x-input-label value="Description" class="text-xs uppercase tracking-wide" />
                    <textarea x-model="newCategoryDescription" rows="2"
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary"></textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button type="button" x-on:click="submitNewCategory()" x-bind:disabled="savingCategory || ! newCategoryName.trim()">
                    <span x-text="savingCategory ? 'Adding…' : 'Add Category'"></span>
                </x-primary-button>
            </div>
        </div>
    </x-modal>

    <div>
        <x-input-label for="lms-content-editor" value="Content" class="text-xs uppercase tracking-wide" />
        <textarea id="lms-content-editor" name="content" rows="20" data-upload-url="{{ route('admin.lms.articles.upload-image') }}">{{ old('content', $article->content ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('content')" class="mt-2" />
    </div>

    <label class="flex items-center gap-2 text-sm text-secondary-dark">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $article->is_published ?? true)) class="rounded border-app-border text-primary focus:ring-primary">
        Published (visible to clients)
    </label>
</div>
