<div class="flex items-center justify-between">
    <div>
        <h2 class="text-lg font-semibold text-secondary-dark">Document Field Template</h2>
        <p class="text-sm text-secondary">Group the documents every client must submit for this product — e.g. Billing, Development</p>
    </div>
    <div class="flex gap-3">
        <x-secondary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-document-group')">
            <x-icon name="plus" class="w-4 h-4" />
            Add Group
        </x-secondary-button>
        @if ($product->documentGroups->isNotEmpty())
            <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-document-field')">
                <x-icon name="plus" class="w-4 h-4" />
                Add Field
            </x-primary-button>
        @endif
    </div>
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
            @forelse ($product->documentGroups as $group)
                <tr class="bg-surface-alt">
                    <td colspan="5" class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-secondary-dark">
                        <span class="inline-flex items-center gap-2">
                            {{ $group->name }}
                            @if ($group->is_mandatory)
                                <x-badge classes="bg-warning-light text-warning">Mandatory</x-badge>
                            @endif
                        </span>
                    </td>
                    <td class="px-4 py-2 text-right">
                        <form method="POST" action="{{ route('admin.products.document-groups.destroy', ['product' => $product, 'documentGroup' => $group]) }}"
                            onsubmit="return confirm('Remove this group and all its fields?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-secondary hover:text-danger" title="Remove group">
                                <x-icon name="trash" class="w-4 h-4" />
                            </button>
                        </form>
                    </td>
                </tr>
                @forelse ($group->fields as $field)
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
                        <td colspan="6" class="px-4 py-6 text-center text-secondary">No fields in this group yet.</td>
                    </tr>
                @endforelse
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-secondary">
                        No document groups yet. Click "Add Group" to create one (e.g. Billing, Development).
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<x-modal name="add-document-group" :show="$errors->has('name')" focusable>
    <form method="POST" action="{{ route('admin.products.document-groups.store', $product) }}" class="p-6">
        @csrf

        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-secondary-dark">Add Document Group</h2>
            <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>

        <div class="mt-6">
            <x-input-label for="group_name" value="Group Name *" class="text-xs uppercase tracking-wide" />
            <x-text-input id="group_name" name="name" class="mt-1.5" placeholder="e.g. Billing, Development" :value="old('name')" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <label class="mt-5 flex items-center gap-2 text-sm text-secondary-dark">
            <input type="checkbox" name="is_mandatory" value="1" class="rounded border-app-border text-primary focus:ring-primary">
            Mandatory for onboarding
        </label>
        <p class="mt-1 text-xs text-secondary">The onboarding process cannot be completed until all required details are submitted.</p>

        <div class="mt-8 flex justify-end gap-3">
            <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
            <x-primary-button>Add Group</x-primary-button>
        </div>
    </form>
</x-modal>

<x-modal name="add-document-field" :show="$errors->hasAny(['label', 'field_type', 'product_document_group_id'])" focusable>
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
                <x-input-label for="product_document_group_id" value="Group *" class="text-xs uppercase tracking-wide" />
                <select id="product_document_group_id" name="product_document_group_id" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                    @foreach ($product->documentGroups as $group)
                        <option value="{{ $group->id }}" @selected(old('product_document_group_id') == $group->id)>{{ $group->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('product_document_group_id')" class="mt-2" />
            </div>

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
            dragging = e.target.closest('tr[data-id]');
            e.dataTransfer.effectAllowed = 'move';
        });

        body.addEventListener('dragover', (e) => {
            e.preventDefault();
            const target = e.target.closest('tr[data-id]');
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
