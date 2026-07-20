<x-client-layout title="Customization">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">Customization Requests</h1>
            <p class="mt-1 text-sm text-secondary">Request custom features for any of your projects and track their status here</p>
        </div>
        @if ($projects->isNotEmpty())
            <x-primary-button type="button" class="shrink-0" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-customization-request')">
                <x-icon name="plus" class="w-4 h-4" />
                Add Request Customization
            </x-primary-button>
        @endif
    </div>

    <div class="mt-6 space-y-4">
        @forelse ($projects as $project)
            @php $pendingCount = $project->customizationRequests->where('status', 'pending')->count(); @endphp
            <div class="rounded-xl border border-app-border bg-white" x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                            <x-icon name="settings" class="w-4 h-4" />
                        </span>
                        <div>
                            <h2 class="font-semibold text-secondary-dark">{{ $project->project_name }}</h2>
                            <p class="text-xs text-secondary">{{ $project->product->name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @if ($pendingCount > 0)
                            <x-badge classes="bg-warning-light text-warning">{{ $pendingCount }} pending</x-badge>
                        @else
                            <x-badge classes="bg-surface-alt text-secondary">{{ $project->customizationRequests->count() }} requests</x-badge>
                        @endif
                        <x-icon name="chevron-down" class="w-4 h-4 shrink-0 text-secondary transition" x-bind:class="open && '-rotate-180'" />
                    </div>
                </button>

                <div x-show="open" x-cloak class="space-y-3 border-t border-app-border p-5">
                    @forelse ($project->customizationRequests as $customizationRequest)
                        @php $badge = customization_status_badge($customizationRequest->status); @endphp
                        <div class="rounded-xl border border-app-border bg-white p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h4 class="font-medium text-secondary-dark">{{ $customizationRequest->title }}</h4>
                                    <p class="mt-1 text-sm text-secondary">{{ $customizationRequest->description }}</p>
                                    <p class="mt-2 text-xs text-secondary/70">Submitted {{ $customizationRequest->created_at->format('d-M-Y') }}</p>
                                </div>
                                <x-badge :classes="$badge['classes']" class="shrink-0">{{ $badge['label'] }}</x-badge>
                            </div>

                            @if ($customizationRequest->status !== 'pending' && $customizationRequest->admin_notes)
                                <div class="mt-3 rounded-lg px-3 py-2 text-sm {{ $customizationRequest->status === 'rejected' ? 'bg-danger-light text-danger' : ($customizationRequest->status === 'partial' ? 'bg-primary-light text-primary' : 'bg-success-light text-success') }}">
                                    <span class="font-medium">Response from our team:</span> {{ $customizationRequest->admin_notes }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-app-border bg-white p-6 text-center text-sm text-secondary">
                            No customization requests for this project yet.
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                You don't have any projects assigned yet.
            </div>
        @endforelse
    </div>

    @if ($projects->isNotEmpty())
        <x-modal name="add-customization-request" :show="$errors->any()" focusable>
            <form method="POST" action="{{ route('client.customization-requests.store') }}" class="p-6">
                @csrf

                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-secondary-dark">Request a Customization</h2>
                    <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <div class="mt-6 space-y-5">
                    <div>
                        <x-input-label for="customization_project_id" value="Project *" class="text-xs uppercase tracking-wide" />
                        <select id="customization_project_id" name="project_id" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary" required>
                            <option value="" disabled selected>Select a project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" @selected(old('project_id') == $project->id)>
                                    {{ $project->project_name }} ({{ $project->product->name }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="customization_title" value="Title *" class="text-xs uppercase tracking-wide" />
                        <x-text-input id="customization_title" name="title" class="mt-1.5" :value="old('title')" placeholder="e.g. Add WhatsApp notifications" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="customization_description" value="What would you like customized? *" class="text-xs uppercase tracking-wide" />
                        <textarea id="customization_description" name="description" rows="4" placeholder="Describe the feature or change you need..."
                            class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary" required>{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                    <x-primary-button>Submit Request</x-primary-button>
                </div>
            </form>
        </x-modal>
    @endif
</x-client-layout>
