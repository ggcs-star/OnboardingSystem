<x-admin-layout title="Ongoing Projects">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Ongoing Projects</h1>
        <p class="mt-1 text-sm text-secondary">Every brand/project across all clients, with its documents and pipeline stage</p>
    </div>

    <form method="GET" class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
        <div class="relative flex-1 sm:min-w-[220px]">
            <x-icon name="search"
                class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary" />
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search client, project, brand..."
                class="w-full rounded-lg border-app-border pl-10 text-sm shadow-sm focus:border-primary focus:ring-primary">
        </div>
        <select name="product" onchange="this.form.submit()"
            class="rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
            <option value="">All Products</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected(request('product') == $product->id)>{{ $product->name }}</option>
            @endforeach
        </select>
        <select name="stage" onchange="this.form.submit()"
            class="rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
            <option value="">All Stages</option>
            @foreach (\App\Models\Project::STAGES as $key => $label)
                <option value="{{ $key }}" @selected(request('stage') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()"
            class="rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
            <option value="">All Statuses</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="blocked" @selected(request('status') === 'blocked')>On Hold</option>
        </select>
        @if ($filterClient)
            <input type="hidden" name="client" value="{{ $filterClient }}">
        @endif
        <button type="submit" class="hidden"></button>
    </form>

    <div class="mt-6 overflow-x-auto rounded-xl border border-app-border bg-white">
        <table class="min-w-full divide-y divide-app-border text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                    <th class="px-4 py-3">Client</th>
                    <th class="px-4 py-3">Project</th>
                    <th class="px-4 py-3">Brand</th>
                    <th class="px-4 py-3">Documents</th>
                    <th class="px-4 py-3">Stage</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-app-border">
                @forelse ($projects as $project)
                    @php
                        [$docsDone, $docsTotal] = $project->documentsProgress();
                        $stageBadge = project_stage_badge($project->current_stage, $project->status);
                    @endphp
                    <tr class="cursor-pointer hover:bg-surface-alt" onclick="window.location = '{{ route('admin.projects.show', $project) }}'">
                        <td class="px-4 py-3">
                            <span class="font-medium text-secondary-dark">{{ $project->client->company_name }}</span>
                        </td>
                        <td class="px-4 py-3 text-secondary">{{ $project->product->name }}</td>
                        <td class="px-4 py-3 text-secondary-dark">{{ $project->brand_name ?? $project->project_name }}</td>
                        <td class="px-4 py-3">
                            <span class="{{ $docsDone === $docsTotal && $docsTotal > 0 ? 'text-success' : 'text-warning' }} font-medium">
                                {{ $docsDone }}/{{ $docsTotal }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <x-badge :classes="$stageBadge['classes']" dot>{{ $stageBadge['label'] }}</x-badge>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-secondary">No projects yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $projects->links() }}
    </div>
</x-admin-layout>
