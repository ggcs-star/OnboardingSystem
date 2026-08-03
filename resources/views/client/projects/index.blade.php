<x-client-layout title="Projects">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Projects</h1>
        <p class="mt-1 text-sm text-secondary">Every project you're onboarding with us</p>
    </div>

    <form method="GET" class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
        <div class="relative flex-1 sm:min-w-[220px]">
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary" />
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search project, brand..."
                class="w-full rounded-lg border-app-border pl-10 text-sm shadow-sm focus:border-primary focus:ring-primary">
        </div>
        <select name="product" onchange="this.form.submit()" class="rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
            <option value="">All Products</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected(request('product') == $product->id)>{{ $product->name }}</option>
            @endforeach
        </select>
        <select name="stage" onchange="this.form.submit()" class="rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
            <option value="">All Stages</option>
            @foreach (\App\Models\Project::STAGES as $key => $label)
                <option value="{{ $key }}" @selected(request('stage') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()" class="rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
            <option value="">All Statuses</option>
            @foreach (\App\Models\Project::STATUSES as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="hidden"></button>
        <a href="{{ route('client.projects.index') }}"
            class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-app-border px-4 py-2 text-sm font-medium text-secondary-dark hover:bg-surface-alt">
            <x-icon name="refresh-cw" class="w-3.5 h-3.5" />
            Reset Filters
        </a>
    </form>

    <div class="mt-6 overflow-x-auto rounded-xl border border-app-border bg-white">
        <table class="min-w-full divide-y divide-app-border text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                    <th class="px-5 py-3">Project</th>
                    <th class="px-5 py-3">Product</th>
                    <th class="px-5 py-3">Stage</th>
                    <th class="px-5 py-3">Documents</th>
                    <th class="px-5 py-3">Salesperson</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-app-border">
                @forelse ($projects as $project)
                    @php
                        [$docsDone, $docsTotal] = $project->documentsProgress();
                        $docsPct = $docsTotal ? round($docsDone / $docsTotal * 100) : 0;
                    @endphp
                    <tr class="hover:bg-surface-alt/60">
                        <td class="px-5 py-3">
                            <a href="{{ route('client.projects.show', $project) }}" class="font-medium text-primary hover:underline">
                                {{ $project->brand_name ?? $project->project_name }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-secondary">{{ $project->product->name }}</td>
                        <td class="px-5 py-3">
                            <x-project-timeline :stage="$project->current_stage" :status="$project->status" />
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="h-2 w-24 shrink-0 overflow-hidden rounded-full bg-primary-light">
                                    <div class="h-2 rounded-full {{ $docsDone === $docsTotal && $docsTotal > 0 ? 'bg-success' : 'bg-primary' }}" style="width: {{ max($docsPct, $docsTotal ? 2 : 0) }}%"></div>
                                </div>
                                <span class="tabular-nums text-xs text-secondary">{{ $docsDone }}/{{ $docsTotal }} · {{ $docsPct }}%</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            @if ($project->salesEmployee)
                                <p class="font-medium text-secondary-dark">{{ $project->salesEmployee->name }}</p>
                                <p class="text-xs text-secondary">
                                    {{ $project->salesEmployee->phone }}
                                    @if ($project->salesEmployee->email)
                                        · {{ $project->salesEmployee->email }}
                                    @endif
                                </p>
                            @else
                                <span class="text-xs text-secondary">Not attributed</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('client.projects.documents', $project) }}" title="Upload Documents"
                                    class="inline-flex items-center justify-center rounded-lg border border-app-border p-1.5 text-secondary hover:bg-surface-alt hover:text-primary">
                                    <x-icon name="file-text" class="w-3.5 h-3.5" />
                                </a>
                                <a href="{{ route('client.projects.policies', $project) }}" title="Policies"
                                    class="inline-flex items-center justify-center rounded-lg border border-app-border p-1.5 text-secondary hover:bg-surface-alt hover:text-primary">
                                    <x-icon name="shield" class="w-3.5 h-3.5" />
                                </a>
                                <form method="POST" action="{{ route('client.projects.hold', $project) }}"
                                    onsubmit="return confirm('{{ $project->status === 'blocked' ? 'Resume this project?' : 'Put this project on hold?' }}');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="{{ $project->status === 'blocked' ? 'Resume Project' : 'Hold Project' }}"
                                        class="inline-flex items-center justify-center rounded-lg border border-app-border p-1.5 text-secondary hover:bg-surface-alt hover:text-primary">
                                        <x-icon :name="$project->status === 'blocked' ? 'play-circle' : 'pause-circle'" class="w-3.5 h-3.5" />
                                    </button>
                                </form>
                                <a href="{{ route('client.projects.show', ['project' => $project, 'readonly' => 1]) }}" title="View Project"
                                    class="inline-flex items-center justify-center rounded-lg bg-primary p-1.5 text-white hover:bg-primary-dark">
                                    <x-icon name="eye" class="w-3.5 h-3.5" />
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-sm text-secondary">
                            @if (request()->anyFilled(['search', 'product', 'stage', 'status']))
                                No projects match your filters.
                            @else
                                You don't have any projects assigned yet. Your onboarding team will notify you once one is created.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-client-layout>
