<x-admin-layout title="Training">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Training Progress</h1>
        <p class="mt-1 text-sm text-secondary">See how far each client has gotten through their product's training videos</p>
    </div>

    <div class="mt-6 space-y-6">
        @forelse ($products as $product)
            <div class="overflow-hidden rounded-xl border border-app-border bg-white">
                <div class="flex items-center justify-between gap-3 border-b border-app-border bg-surface-alt px-5 py-3">
                    <div class="flex items-center gap-2">
                        <x-badge classes="bg-white text-secondary-dark">{{ $product->name }}</x-badge>
                        <span class="text-xs text-secondary">{{ $product->training->count() }} training videos</span>
                    </div>
                </div>

                <table class="min-w-full divide-y divide-app-border text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                            <th class="px-4 py-3">Client</th>
                            <th class="px-4 py-3">Project(s)</th>
                            <th class="px-4 py-3">Videos Watched</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-app-border">
                        @forelse ($product->clientRows as $row)
                            @php
                                $pct = $row->total > 0 ? round(($row->done / $row->total) * 100) : 0;
                            @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.projects.show', ['project' => $row->firstProject, 'tab' => 'training']) }}" class="font-medium text-secondary-dark hover:text-primary">
                                        {{ $row->client->company_name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-secondary">{{ $row->projectNames->implode(', ') }}</td>
                                <td class="px-4 py-3">
                                    <div class="h-1.5 w-32 rounded-full bg-secondary-light">
                                        <div class="h-1.5 rounded-full {{ $pct === 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <div class="mt-1 text-xs text-secondary">{{ $pct }}% · {{ $row->done }}/{{ $row->total }} videos</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-secondary">No clients for this product yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                No products yet.
            </div>
        @endforelse
    </div>
</x-admin-layout>
