<div class="flex items-center justify-between">
    <div>
        <h2 class="text-lg font-semibold text-secondary-dark">Document Field Template</h2>
        <p class="text-sm text-secondary">Define what documents every client must submit for this product</p>
    </div>
    <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-document-field')">
        <x-icon name="plus" class="w-4 h-4" />
        Add Field
    </x-primary-button>
</div>

<div class="mt-6 overflow-x-auto rounded-xl border border-app-border bg-white">
    <table class="min-w-full divide-y divide-app-border text-sm">
        <thead>
            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                <th class="w-12 px-4 py-3">#</th>
                <th class="px-4 py-3">Field Label</th>
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Placeholder</th>
                <th class="px-4 py-3">Required</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody id="document-fields-body"
            data-reorder-url="{{ route('admin.products.document-fields.reorder', $product) }}"
            class="divide-y divide-app-border">
            @forelse ($product->documentFields as $field)
                @php $badge = document_field_type_badge($field->field_type); @endphp
                <tr draggable="true" data-id="{{ $field->id }}" class="cursor-move bg-white">
                    <td class="px-4 py-3 text-secondary">
                        <span class="flex items-center gap-2">
                            <x-icon name="grip" class="w-4 h-4 text-secondary/60" />
                            {{ $loop->iteration }}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-medium text-secondary-dark">{{ $field->label }}</td>
                    <td class="px-4 py-3">
                        <x-badge :classes="$badge['classes']">{{ $badge['label'] }}</x-badge>
                    </td>
                    <td class="px-4 py-3 text-secondary">{{ $field->placeholder }}</td>
                    <td class="px-4 py-3">
                        <form method="POST" action="{{ route('admin.products.document-fields.update', ['product' => $product, 'documentField' => $field]) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="{{ $field->required ? 'text-primary' : 'text-secondary/50' }}" title="Toggle required">
                                <x-icon :name="$field->required ? 'eye' : 'eye-off'" class="w-4 h-4" />
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" action="{{ route('admin.products.document-fields.destroy', ['product' => $product, 'documentField' => $field]) }}"
                            onsubmit="return confirm('Remove this field?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-secondary hover:text-danger">
                                <x-icon name="trash" class="w-4 h-4" />
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-secondary">No document fields yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<x-modal name="add-document-field" :show="$errors->any()" focusable>
    <form method="POST" action="{{ route('admin.products.document-fields.store', $product) }}" class="p-6">
        @csrf

        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-secondary-dark">Add Document Field</h2>
            <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>

        <div class="mt-6 space-y-5">
            <div>
                <x-input-label for="label" value="Field Label *" class="text-xs uppercase tracking-wide" />
                <x-text-input id="label" name="label" class="mt-1.5" placeholder="e.g. Company Logo" :value="old('label')" required />
                <x-input-error :messages="$errors->get('label')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="field_type" value="Field Type *" class="text-xs uppercase tracking-wide" />
                <select id="field_type" name="field_type" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                    <option value="text">Text</option>
                    <option value="number">Number</option>
                    <option value="image">Image</option>
                    <option value="pdf">PDF</option>
                    <option value="key">Key / Credential</option>
                </select>
                <x-input-error :messages="$errors->get('field_type')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="placeholder" value="Placeholder" class="text-xs uppercase tracking-wide" />
                <x-text-input id="placeholder" name="placeholder" class="mt-1.5" placeholder="Helper text shown to the client" :value="old('placeholder')" />
                <x-input-error :messages="$errors->get('placeholder')" class="mt-2" />
            </div>

            <label class="flex items-center gap-2 text-sm text-secondary-dark">
                <input type="checkbox" name="required" value="1" checked class="rounded border-app-border text-primary focus:ring-primary">
                Required field
            </label>
        </div>

        <div class="mt-8 flex justify-end gap-3">
            <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
            <x-primary-button>Add Field</x-primary-button>
        </div>
    </form>
</x-modal>

<script>
    (function () {
        const body = document.getElementById('document-fields-body');
        if (!body) return;
        let dragging = null;

        body.addEventListener('dragstart', (e) => {
            dragging = e.target.closest('tr');
            e.dataTransfer.effectAllowed = 'move';
        });

        body.addEventListener('dragover', (e) => {
            e.preventDefault();
            const target = e.target.closest('tr');
            if (!target || target === dragging) return;
            const rect = target.getBoundingClientRect();
            const next = (e.clientY - rect.top) / rect.height > 0.5;
            body.insertBefore(dragging, next ? target.nextSibling : target);
        });

        body.addEventListener('drop', (e) => {
            e.preventDefault();
            const order = Array.from(body.querySelectorAll('tr[data-id]')).map((row) => row.dataset.id);
            axios.post(body.dataset.reorderUrl, { order }).catch(() => window.location.reload());
        });
    })();
</script>
