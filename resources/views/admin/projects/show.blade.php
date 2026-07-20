@php
    $tabs = [
        'documents' => ['label' => 'Documents', 'icon' => 'file-text'],
        'training' => ['label' => 'Training', 'icon' => 'video'],
        'renewal' => ['label' => 'Renewal', 'icon' => 'refresh-cw'],
        'customization' => ['label' => 'Customization', 'icon' => 'settings'],
    ];
    $activeTab = request('tab', 'documents');
    if (! array_key_exists($activeTab, $tabs)) {
        $activeTab = 'documents';
    }
    $stageBadge = project_stage_badge($project->current_stage, $project->status);
    $currentIndex = array_search($project->current_stage, array_keys(\App\Models\Project::STAGES), true);
@endphp

<x-admin-layout :title="$project->project_name">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.projects.index') }}" class="hover:text-primary">Projects</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">{{ $project->project_name }}</span>
    </nav>

    <div class="mt-3 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-semibold text-secondary-dark">{{ $project->project_name }}</h1>
                <x-badge :classes="$stageBadge['classes']" dot>{{ $stageBadge['label'] }}</x-badge>
            </div>
            <p class="text-sm text-secondary">
                <a href="{{ route('admin.products.show', $project->product) }}" class="text-primary hover:underline">{{ $project->product->name }}</a>
                · {{ $project->client->company_name }}
            </p>
        </div>

        <div class="flex shrink-0 gap-3">
            <x-secondary-button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-project')">
                <x-icon name="settings" class="w-4 h-4" />
                Edit Project
            </x-secondary-button>

            <form method="POST" action="{{ route('admin.projects.toggle-blocked', $project) }}">
                @csrf
                @if ($project->status === 'blocked')
                    <x-primary-button>Unblock Project</x-primary-button>
                @else
                    <x-danger-button>Mark Blocked</x-danger-button>
                @endif
            </form>
        </div>
    </div>

    <x-modal name="edit-project" :show="$errors->any()" focusable>
        <form method="POST" action="{{ route('admin.projects.update', $project) }}" class="p-6">
            @csrf
            @method('PUT')

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-secondary-dark">Edit Project</h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <div class="mt-6 space-y-5">
                <div>
                    <x-input-label value="Product" class="text-xs uppercase tracking-wide" />
                    <p class="mt-1.5 rounded-lg border border-app-border bg-surface-alt px-3 py-2 text-sm text-secondary-dark">{{ $project->product->name }}</p>
                    <p class="mt-1 text-xs text-secondary">The product can't be changed after a project is created — its document/training template is already attached.</p>
                </div>

                <div>
                    <x-input-label value="Client" class="text-xs uppercase tracking-wide" />
                    <p class="mt-1.5 rounded-lg border border-app-border bg-surface-alt px-3 py-2 text-sm text-secondary-dark">{{ $project->client->company_name }}</p>
                </div>

                <div>
                    <x-input-label for="edit_project_name" value="Project Name *" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="edit_project_name" name="project_name" class="mt-1.5" :value="old('project_name', $project->project_name)" required />
                    <x-input-error :messages="$errors->get('project_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="edit_expected_live_date" value="Expected Live Date" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="edit_expected_live_date" name="expected_live_date" type="date" class="mt-1.5"
                        :value="old('expected_live_date', $project->expected_live_date?->format('Y-m-d'))" />
                    <x-input-error :messages="$errors->get('expected_live_date')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="edit_actual_live_date" value="Actual Live Date" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="edit_actual_live_date" name="actual_live_date" type="date" class="mt-1.5"
                        :value="old('actual_live_date', $project->actual_live_date?->format('Y-m-d'))" />
                    <x-input-error :messages="$errors->get('actual_live_date')" class="mt-2" />
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button>Save Changes</x-primary-button>
            </div>
        </form>
    </x-modal>

    <div class="mt-6 rounded-xl border border-app-border bg-white p-6">
        <p class="mb-4 text-xs font-semibold uppercase tracking-wide text-secondary">Admin Timeline — click a stage to move the project there</p>
        <div class="flex flex-wrap items-center gap-2">
            @foreach (\App\Models\Project::STAGES as $key => $label)
                @php
                    $index = array_search($key, array_keys(\App\Models\Project::STAGES), true);
                    $isDone = $index < $currentIndex;
                    $isCurrent = $index === $currentIndex;
                @endphp
                <form method="POST" action="{{ route('admin.projects.stage', $project) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="stage" value="{{ $key }}">
                    <button type="submit"
                        class="flex items-center gap-2 rounded-lg border px-3 py-2 text-sm font-medium transition
                            {{ $isCurrent ? 'border-primary bg-primary-light text-primary' : ($isDone ? 'border-success/30 bg-success-light text-success' : 'border-app-border text-secondary hover:bg-surface-alt') }}">
                        @if ($isDone)
                            <x-icon name="check" class="w-4 h-4" />
                        @endif
                        {{ $label }}
                    </button>
                </form>
                @if (! $loop->last)
                    <span class="h-px w-4 bg-app-border"></span>
                @endif
            @endforeach
        </div>
    </div>

    <x-client-progress-timeline
        class="mt-6"
        :project="$project"
        heading="Client Timeline — what the client sees on their side"
        :footer="$project->client->company_name . ' is currently in the ' . $stageBadge['label'] . ' stage.'"
    />

    <div class="mt-6 border-b border-app-border">
        <nav class="-mb-px flex gap-6 overflow-x-auto">
            @foreach ($tabs as $key => $tab)
                <x-tab-link :href="route('admin.projects.show', ['project' => $project, 'tab' => $key])" :active="$activeTab === $key" :icon="$tab['icon']">
                    {{ $tab['label'] }}
                </x-tab-link>
            @endforeach
        </nav>
    </div>

    <div class="mt-6">
        @include('admin.projects.tabs.' . $activeTab)
    </div>
</x-admin-layout>
