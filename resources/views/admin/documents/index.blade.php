<x-admin-layout title="Documents">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Documents</h1>
        <p class="mt-1 text-sm text-secondary">Review and approve client document submissions across all projects</p>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        <x-stat-card label="Total Fields" :value="$stats['total_fields']" />
        <x-stat-card label="Pending" :value="$stats['pending']" tint="warning" />
        <x-stat-card label="Submitted" :value="$stats['submitted']" />
        <x-stat-card label="Approved" :value="$stats['approved']" />
        <x-stat-card label="Rejected" :value="$stats['rejected']" tint="danger" />
    </div>

    <form method="GET" class="mt-6 flex flex-col gap-3 sm:flex-row">
        <div class="relative flex-1">
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary" />
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search project, client, field..."
                class="w-full rounded-lg border-app-border pl-10 text-sm shadow-sm focus:border-primary focus:ring-primary">
        </div>
        <select name="product" onchange="this.form.submit()" class="rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
            <option value="">All Products</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected(request('product') == $product->id)>{{ $product->name }}</option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()" class="rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
            <option value="">All Status</option>
            @foreach (['pending', 'submitted', 'approved', 'rejected'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <button type="submit" class="hidden"></button>
    </form>

    <div class="mt-6 overflow-x-auto rounded-xl border border-app-border bg-white">
        <table class="min-w-full divide-y divide-app-border text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                    <th class="px-4 py-3">Project / Client</th>
                    <th class="px-4 py-3">Product</th>
                    <th class="px-4 py-3">Field</th>
                    <th class="px-4 py-3">Submitted Value</th>
                    <th class="px-4 py-3">Submitted</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-app-border">
                @forelse ($values as $value)
                    @php
                        $badge = document_value_status_badge($value->status);
                        $typeBadge = document_field_type_badge($value->documentField->field_type);
                    @endphp
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.projects.show', ['project' => $value->project, 'tab' => 'documents']) }}" class="font-medium text-secondary-dark hover:text-primary">
                                {{ $value->project->project_name }}
                            </a>
                            <div class="text-xs text-secondary">{{ $value->project->client->company_name }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <x-badge classes="bg-surface-alt text-secondary">{{ $value->project->product->name }}</x-badge>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2 font-medium text-secondary-dark">
                                <x-icon :name="$value->documentField->field_type === 'image' ? 'file-text' : 'file-text'" class="w-4 h-4 text-secondary/60" />
                                {{ $value->documentField->label }}
                                @if ($value->documentField->required)
                                    <span class="text-danger">*</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-secondary">
                            @if ($value->file)
                                <a href="{{ asset('storage/' . $value->file) }}" target="_blank" class="text-primary hover:underline">View file</a>
                            @elseif ($value->value)
                                {{ Str::limit($value->value, 30) }}
                            @else
                                <span class="text-secondary/60">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-secondary">{{ $value->updated_at->format('d-M-Y') }}</td>
                        <td class="px-4 py-3">
                            <x-badge :classes="$badge['classes']">{{ $badge['label'] }}</x-badge>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if (in_array($value->status, ['submitted', 'rejected'], true))
                                <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'review-{{ $value->id }}')"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-app-border px-3 py-1.5 text-xs font-medium text-secondary-dark hover:bg-surface-alt">
                                    <x-icon name="eye" class="w-3.5 h-3.5" />
                                    Review
                                </button>

                                <x-modal :name="'review-' . $value->id" focusable>
                                    <div class="p-6">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h2 class="text-lg font-semibold text-secondary-dark">Review Submission</h2>
                                                <p class="text-xs text-secondary">{{ $value->project->project_name }} · {{ $value->project->client->company_name }}</p>
                                            </div>
                                            <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                                                <x-icon name="x" class="w-5 h-5" />
                                            </button>
                                        </div>

                                        <div class="mt-5 grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <p class="text-xs uppercase tracking-wide text-secondary">Field</p>
                                                <p class="mt-1 font-medium text-secondary-dark">{{ $value->documentField->label }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs uppercase tracking-wide text-secondary">Type</p>
                                                <p class="mt-1 font-medium text-secondary-dark">{{ $typeBadge['label'] }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs uppercase tracking-wide text-secondary">Product</p>
                                                <p class="mt-1 font-medium text-secondary-dark">{{ $value->project->product->name }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs uppercase tracking-wide text-secondary">Submitted On</p>
                                                <p class="mt-1 font-medium text-secondary-dark">{{ $value->updated_at->format('d-M-Y') }}</p>
                                            </div>
                                        </div>

                                        <form method="POST" action="{{ route('admin.projects.document-values.update', ['project' => $value->project, 'documentValue' => $value]) }}" class="mt-5">
                                            @csrf
                                            @method('PATCH')

                                            <x-input-label value="Submitted Value" class="text-xs uppercase tracking-wide" />
                                            <div class="mt-1.5 rounded-lg border border-app-border bg-surface-alt px-3 py-2 text-sm text-secondary-dark">
                                                @if ($value->file)
                                                    <a href="{{ asset('storage/' . $value->file) }}" target="_blank" class="text-primary hover:underline">View uploaded file</a>
                                                @else
                                                    {{ $value->value ?: '—' }}
                                                @endif
                                            </div>

                                            <div class="mt-6 flex justify-between">
                                                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                                <div class="flex gap-3">
                                                    <button type="submit" name="status" value="rejected" class="inline-flex items-center gap-2 rounded-lg bg-danger-light px-4 py-2.5 text-sm font-medium text-danger hover:opacity-80">
                                                        <x-icon name="x" class="w-4 h-4" />
                                                        Reject
                                                    </button>
                                                    <button type="submit" name="status" value="approved" class="inline-flex items-center gap-2 rounded-lg bg-success px-4 py-2.5 text-sm font-medium text-white hover:opacity-90">
                                                        <x-icon name="check" class="w-4 h-4" />
                                                        Approve
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </x-modal>
                            @else
                                <span class="text-xs text-secondary/60">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-secondary">No document submissions yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $values->links() }}
    </div>
</x-admin-layout>
