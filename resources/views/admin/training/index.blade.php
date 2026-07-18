<x-admin-layout title="Training">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Training Progress</h1>
        <p class="mt-1 text-sm text-secondary">See how far each client has gotten through their product's training videos</p>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-app-border bg-white">
        <table class="min-w-full divide-y divide-app-border text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                    <th class="px-4 py-3">Project</th>
                    <th class="px-4 py-3">Client</th>
                    <th class="px-4 py-3">Product</th>
                    <th class="px-4 py-3">Videos Watched</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-app-border">
                @forelse ($projects as $project)
                    @php
                        [$done, $total] = $project->trainingProgressCount();
                        $pct = $total > 0 ? round(($done / $total) * 100) : 0;
                    @endphp
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.projects.show', ['project' => $project, 'tab' => 'training']) }}" class="font-medium text-secondary-dark hover:text-primary">
                                {{ $project->project_name }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-secondary">{{ $project->client->company_name }}</td>
                        <td class="px-4 py-3">
                            <x-badge classes="bg-surface-alt text-secondary">{{ $project->product->name }}</x-badge>
                        </td>
                        <td class="px-4 py-3">
                            <div class="h-1.5 w-32 rounded-full bg-secondary-light">
                                <div class="h-1.5 rounded-full {{ $pct === 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="mt-1 text-xs text-secondary">{{ $pct }}% · {{ $done }}/{{ $total }} videos</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-secondary">No projects yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $projects->links() }}
    </div>
</x-admin-layout>
