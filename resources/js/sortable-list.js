/**
 * Native HTML5 drag-and-drop reordering (no library) — the whole row is
 * draggable, same pattern already used for training videos
 * (resources/views/admin/training/index.blade.php). Markup convention:
 *
 *   <div data-sortable data-sortable-url="{{ route(...) }}">
 *     <div data-sortable-item draggable="true" data-sortable-id="{{ $id }}" class="cursor-move">
 *       ...row content (grip icon is purely decorative, edit/delete buttons, etc)...
 *     </div>
 *   </div>
 *
 * On drop, the new DOM order of `[data-sortable-item]` ids is POSTed to the
 * container's `data-sortable-url` as `{ ids: [...] }`.
 *
 * Sortable containers can be nested (e.g. a sub-category's own article list
 * lives inside a sub-category row, which itself lives inside the
 * sub-categories list) — every handler stops propagation once it takes
 * ownership of an event so a drag inside a nested list never also gets
 * processed by the ancestor list's listeners.
 */
function initSortableLists() {
    document.querySelectorAll('[data-sortable]').forEach((list) => {
        if (list.dataset.sortableInit) {
            return;
        }
        list.dataset.sortableInit = '1';

        let dragging = null;

        list.addEventListener('dragstart', (e) => {
            const item = e.target.closest('[data-sortable-item]');

            if (!item || item.parentElement !== list) {
                e.preventDefault();
                return;
            }

            e.stopPropagation();
            dragging = item;
            item.classList.add('opacity-40');
            e.dataTransfer.effectAllowed = 'move';
        });

        list.addEventListener('dragover', (e) => {
            if (!dragging) {
                return;
            }

            const target = e.target.closest('[data-sortable-item]');
            if (!target || target === dragging || target.parentElement !== list) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            const rect = target.getBoundingClientRect();
            const next = (e.clientY - rect.top) / rect.height > 0.5;
            list.insertBefore(dragging, next ? target.nextSibling : target);
        });

        list.addEventListener('drop', (e) => {
            if (!dragging) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            const ids = [...list.querySelectorAll(':scope > [data-sortable-item]')].map((el) => el.dataset.sortableId);

            window.axios.post(list.dataset.sortableUrl, { ids }).catch(() => window.location.reload());
        });

        list.addEventListener('dragend', () => {
            if (dragging) {
                dragging.classList.remove('opacity-40');
            }
            dragging = null;
        });
    });
}

document.addEventListener('DOMContentLoaded', initSortableLists);

export default initSortableLists;
