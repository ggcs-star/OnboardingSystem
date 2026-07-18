<x-admin-layout title="Projects">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">Projects</h1>
            <p class="mt-1 text-sm text-secondary">Manage all client projects — track documents, timelines, training and renewals</p>
        </div>
        <p class="shrink-0 text-xs text-secondary">Last updated: {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Active Projects" :value="$stats['active_projects']" :hint="$stats['total_projects'] . ' total projects across all products'" />
        <x-stat-card label="Documents Pending" :value="$stats['documents_pending']" hint="Projects with incomplete document submissions" tint="warning" />
        <x-stat-card label="Open Support Tickets" :value="$stats['open_support_tickets']" hint="Unresolved tickets across all projects" tint="danger" />
        <x-stat-card label="Renewals Alert" :value="$stats['renewals_alert']" hint="Expired or expiring within 30 days" tint="danger" />
    </div>

    <form method="GET" class="mt-6 flex flex-col gap-3 lg:flex-row">
        <div class="relative flex-1">
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary" />
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search projects, clients..."
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
        <button type="submit" class="hidden"></button>
        <x-primary-button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'new-project')">
            <x-icon name="plus" class="w-4 h-4" />
            New Project
        </x-primary-button>
    </form>

    <div class="mt-6 overflow-x-auto rounded-xl border border-app-border bg-white">
        <table class="min-w-full divide-y divide-app-border text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                    <th class="px-4 py-3">Project</th>
                    <th class="px-4 py-3">Product</th>
                    <th class="px-4 py-3">Client</th>
                    <th class="px-4 py-3">Stage</th>
                    <th class="px-4 py-3">Client Onboarding</th>
                    <th class="px-4 py-3">Admin Timeline</th>
                    <th class="px-4 py-3">Tickets</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-app-border">
                @forelse ($projects as $project)
                    @php
                        $stageBadge = project_stage_badge($project->current_stage, $project->status);
                        $openTickets = $project->tickets->where('status', 'open')->count();
                        $templateDefined = $project->product->documentFields->isNotEmpty();
                    @endphp
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.projects.show', $project) }}" class="font-medium text-secondary-dark hover:text-primary">
                                {{ $project->project_name }}
                            </a>
                            <div class="text-xs text-secondary">Since {{ $project->created_at->format('d-M-Y') }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.products.show', $project->product) }}" class="text-primary hover:underline">{{ $project->product->name }}</a>
                        </td>
                        <td class="px-4 py-3 text-secondary-dark">{{ $project->client->company_name }}</td>
                        <td class="px-4 py-3">
                            <x-badge :classes="$stageBadge['classes']" dot>{{ $stageBadge['label'] }}</x-badge>
                        </td>
                        <td class="px-4 py-3">
                            <x-client-onboarding-timeline :project="$project" />
                        </td>
                        <td class="px-4 py-3">
                            <x-project-timeline :stage="$project->current_stage" :status="$project->status" :template-defined="$templateDefined" />
                        </td>
                        <td class="px-4 py-3 text-secondary">
                            @if ($openTickets)
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-danger-light text-xs font-semibold text-danger">{{ $openTickets }}</span>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
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

    <x-modal name="new-project" :show="$errors->any()" focusable>
        <form method="POST" action="{{ route('admin.projects.store') }}" class="p-6">
            @csrf

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-secondary-dark">New Project</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <div class="mt-6 space-y-5">
                <div>
                    <x-input-label for="product_id" value="Product *" class="text-xs uppercase tracking-wide" />
                    <select id="product_id" name="product_id" required
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                        <option value="">Select a product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="client_id" value="Client *" class="text-xs uppercase tracking-wide" />
                    <select id="client_id" name="client_id" required
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                        <option value="">Select a client</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>{{ $client->company_name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="project_name" value="Project Name *" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="project_name" name="project_name" class="mt-1.5" placeholder="e.g. AajTak City" :value="old('project_name')" required />
                    <x-input-error :messages="$errors->get('project_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="expected_live_date" value="Expected Live Date" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="expected_live_date" name="expected_live_date" type="date" class="mt-1.5" :value="old('expected_live_date')" />
                    <x-input-error :messages="$errors->get('expected_live_date')" class="mt-2" />
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button>Create Project</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-admin-layout>
