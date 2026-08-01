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
            @foreach (\App\Models\Project::STATUSES as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
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
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-app-border">
                @forelse ($projects as $project)
                    @php
                        [$docsDone, $docsTotal] = $project->documentsProgress();
                        $statusBadge = project_status_badge($project->status);
                        $statusIconColor = match ($project->status) {
                            'blocked' => 'text-warning',
                            'inactive' => 'text-danger',
                            default => 'text-success',
                        };
                    @endphp
                    <tr class="hover:bg-surface-alt">
                        <td class="px-4 py-3">
                            <span class="font-medium text-secondary-dark">{{ $project->client->company_name }}</span>
                        </td>
                        <td class="px-4 py-3 text-secondary">{{ $project->product->name }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.projects.show', $project) }}" class="font-medium text-secondary-dark hover:text-primary">
                                {{ $project->brand_name ?? $project->project_name }}
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            <span class="{{ $docsDone === $docsTotal && $docsTotal > 0 ? 'text-success' : 'text-warning' }} font-medium">
                                {{ $docsDone }}/{{ $docsTotal }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'change-stage-{{ $project->id }}')"
                                class="rounded-md p-1 hover:bg-surface-alt" title="Change Stage">
                                <x-project-timeline :stage="$project->current_stage" :status="$project->status" />
                            </button>
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.projects.status.update', $project) }}">
                                @csrf
                                @method('PATCH')
                                <div class="relative inline-block">
                                    <select name="status" onchange="this.form.submit()"
                                        class="appearance-none rounded-full border-0 py-1.5 pl-3.5 pr-8 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1 {{ $statusBadge['classes'] }}">
                                        @foreach (\App\Models\Project::STATUSES as $key => $label)
                                            <option value="{{ $key }}" @selected($project->status === $key)>&#9679; {{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <x-icon name="chevron-down" class="pointer-events-none absolute right-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 {{ $statusIconColor }}" />
                                </div>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.projects.show', $project) }}" title="View Project"
                                    class="inline-flex items-center justify-center rounded-md border border-primary/30 bg-primary-light p-1.5 text-primary hover:border-primary/60 hover:bg-primary/20">
                                    <x-icon name="eye" class="w-3.5 h-3.5" />
                                </a>
                                <a href="{{ route('admin.projects.documents.index', $project) }}" title="Edit Documents"
                                    class="inline-flex items-center justify-center rounded-md border border-primary/30 bg-primary-light p-1.5 text-primary hover:border-primary/60 hover:bg-primary/20">
                                    <x-icon name="edit" class="w-3.5 h-3.5" />
                                </a>
                                <form method="POST" action="{{ route('admin.projects.destroy', $project) }}"
                                    onsubmit="return confirm('Delete this project? This also removes its documents, renewal history, and tickets.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete Project"
                                        class="inline-flex items-center justify-center rounded-md border border-danger/30 bg-danger-light p-1.5 text-danger hover:border-danger/60 hover:bg-danger/20">
                                        <x-icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <x-modal name="change-stage-{{ $project->id }}" focusable>
                        <form method="POST" action="{{ route('admin.projects.stage.update', $project) }}" class="p-6"
                            onsubmit="return confirm('Change the stage to \'' + document.getElementById('stage-select-{{ $project->id }}').selectedOptions[0].text + '\'? Every stage before it will be marked complete.')">
                            @csrf
                            @method('PATCH')

                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-semibold text-secondary-dark">Change Pipeline Stage</h2>
                                    <p class="text-xs text-secondary">{{ $project->brand_name ?? $project->project_name }}</p>
                                </div>
                                <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                                    <x-icon name="x" class="w-5 h-5" />
                                </button>
                            </div>

                            <div class="mt-6 overflow-x-auto pb-1">
                                <x-project-timeline :stage="$project->current_stage" :status="$project->status" show-labels />
                            </div>

                            <div class="mt-6">
                                <x-input-label value="Set Stage To" class="text-xs uppercase tracking-wide" />
                                <select id="stage-select-{{ $project->id }}" name="stage"
                                    class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                                    @foreach (\App\Models\Project::STAGES as $key => $label)
                                        <option value="{{ $key }}" @selected($project->current_stage === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1.5 text-xs text-secondary">Every stage before the one you pick will be marked complete.</p>
                            </div>

                            <div class="mt-6 flex justify-between">
                                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                <x-primary-button type="submit">Update Stage</x-primary-button>
                            </div>
                        </form>
                    </x-modal>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-secondary">No projects yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $projects->links() }}
    </div>
</x-admin-layout>
