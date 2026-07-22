@php
    $productOptions = $projects
        ->groupBy('product_id')
        ->map(fn ($productProjects) => [
            'label' => $productProjects->first()->product->name,
            'projects' => $productProjects->map(fn ($project) => [
                'id' => $project->id,
                'label' => $project->brand_name ?? $project->project_name,
            ])->values(),
        ]);

    $projectsWithTickets = $projects->filter(fn ($project) => $project->tickets->isNotEmpty())->values();
@endphp

<x-client-layout title="Support">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">Support</h1>
            <p class="mt-1 text-sm text-secondary">Raise a support request for any of your brands and track our responses here</p>
        </div>
        @if ($projects->isNotEmpty())
            <x-primary-button type="button" class="shrink-0" x-data="" x-on:click.prevent="$dispatch('open-modal', 'new-support-ticket')">
                <x-icon name="plus" class="w-4 h-4" />
                New Request
            </x-primary-button>
        @endif
    </div>

    <div class="mt-6 space-y-4">
        @forelse ($projectsWithTickets as $project)
            @php $openCount = $project->tickets->whereNotIn('status', ['resolved', 'closed'])->count(); @endphp
            <div class="rounded-xl border border-app-border bg-white" x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                            <x-icon name="life-buoy" class="w-4 h-4" />
                        </span>
                        <div>
                            <h2 class="font-semibold text-secondary-dark">{{ $project->brand_name ?? $project->project_name }}</h2>
                            <p class="text-xs text-secondary">{{ $project->product->name }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @if ($openCount > 0)
                            <x-badge classes="bg-warning-light text-warning">{{ $openCount }} open</x-badge>
                        @else
                            <x-badge classes="bg-surface-alt text-secondary">{{ $project->tickets->count() }} request{{ $project->tickets->count() === 1 ? '' : 's' }}</x-badge>
                        @endif
                        <x-icon name="chevron-down" class="w-4 h-4 shrink-0 text-secondary transition" x-bind:class="open && '-rotate-180'" />
                    </div>
                </button>

                <div x-show="open" x-cloak class="space-y-3 border-t border-app-border p-5">
                    @foreach ($project->tickets->sortByDesc('created_at') as $ticket)
                        @php
                            $statusBadge = support_ticket_status_badge($ticket->status);
                            $categoryBadge = support_ticket_category_badge($ticket->category);
                            $firstMessage = $ticket->messages->first();
                        @endphp
                        <a href="{{ route('client.support.show', $ticket) }}" class="block rounded-xl border border-app-border bg-white p-5 hover:border-primary">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-medium text-secondary">{{ $ticket->ticket_no }}</span>
                                        <x-badge :classes="$categoryBadge['classes']">{{ $categoryBadge['label'] }}</x-badge>
                                    </div>
                                    <p class="mt-2 text-sm text-secondary-dark">{{ Str::limit($firstMessage?->message, 140) }}</p>
                                    <p class="mt-2 text-xs text-secondary/70">Submitted {{ $ticket->created_at->format('d-M-Y') }}</p>
                                </div>
                                <x-badge :classes="$statusBadge['classes']" class="shrink-0">{{ $statusBadge['label'] }}</x-badge>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                @if ($projects->isEmpty())
                    You don't have any projects assigned yet.
                @else
                    You haven't raised any support requests yet. Click "New Request" to get started.
                @endif
            </div>
        @endforelse
    </div>

    @if ($projects->isNotEmpty())
        <x-modal name="new-support-ticket" :show="$errors->any()" focusable>
            <form method="POST" action="{{ route('client.support.store') }}" class="p-6" x-data="{ productId: '{{ old('product_id') }}', projects: @js($productOptions) }">
                @csrf

                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-secondary-dark">Raise a Support Request</h2>
                    <button type="button" x-on:click="$dispatch('close')" class="text-secondary hover:text-secondary-dark">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <div class="mt-6 space-y-5">
                    <div>
                        <x-input-label value="Product *" class="text-xs uppercase tracking-wide" />
                        <select x-model="productId" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary" required>
                            <option value="" disabled selected>Select a product</option>
                            <template x-for="(group, productId) in projects" :key="productId">
                                <option :value="productId" x-text="group.label"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="support_project_id" value="Brand *" class="text-xs uppercase tracking-wide" />
                        <select id="support_project_id" name="project_id" x-bind:disabled="!productId"
                            class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary disabled:bg-surface-alt disabled:text-secondary" required>
                            <option value="" disabled selected>Select a brand</option>
                            <template x-for="project in (projects[productId]?.projects ?? [])" :key="project.id">
                                <option :value="project.id" x-text="project.label" :selected="project.id == {{ (int) old('project_id') }}"></option>
                            </template>
                        </select>
                        <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="support_category" value="What do you need help with? *" class="text-xs uppercase tracking-wide" />
                        <select id="support_category" name="category" class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary" required>
                            <option value="" disabled selected>Select a category</option>
                            @foreach (\App\Models\SupportTicket::CATEGORIES as $value => $label)
                                <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="support_description" value="Describe your request *" class="text-xs uppercase tracking-wide" />
                        <textarea id="support_description" name="description" rows="4" placeholder="Tell us what's going on..."
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
