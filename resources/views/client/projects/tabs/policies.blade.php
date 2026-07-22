@php
    $brandLabel = $project->brand_name ?? $project->project_name;
@endphp

<div class="space-y-8">
    <div>
        <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary">{{ $project->product->name }} Company Policies</h2>
        <p class="mt-1 text-xs text-secondary">Standard policies defined by our team for {{ $project->product->name }}.</p>

        <div class="mt-4 space-y-4" x-data="{ open: {{ $project->product->policies->first()?->id ?? 'null' }} }">
            @forelse ($project->product->policies as $policy)
                <div class="rounded-xl border border-app-border bg-white">
                    <button type="button" @click="open = (open === {{ $policy->id }} ? null : {{ $policy->id }})"
                        class="flex w-full items-center justify-between gap-3 px-5 py-4 text-left">
                        <span class="flex items-center gap-2 font-medium text-secondary-dark">
                            <x-icon name="shield" class="w-4 h-4 text-primary" />
                            {{ $policy->title }}
                        </span>
                        <x-icon name="chevron-down" class="w-4 h-4 text-secondary transition" x-bind:class="open === {{ $policy->id }} && '-rotate-180'" />
                    </button>

                    <div x-show="open === {{ $policy->id }}" x-cloak class="border-t border-app-border px-5 py-5 text-sm text-secondary-dark whitespace-pre-line">
                        {{ $policy->content ?: 'No content has been added for this policy yet.' }}
                    </div>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-app-border bg-white p-8 text-center text-sm text-secondary">
                    No company policies published yet.
                </div>
            @endforelse
        </div>
    </div>

    <div>
        <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary">{{ $brandLabel }} Policies</h2>
        <p class="mt-1 text-xs text-secondary">Your own policies specific to {{ $brandLabel }} — add anything that only applies to this brand.</p>

        <div class="mt-4 space-y-4" x-data="{ open: {{ $project->policies->first()?->id ?? 'null' }} }">
            @forelse ($project->policies as $policy)
                <div class="rounded-xl border border-app-border bg-white">
                    <button type="button" @click="open = (open === {{ $policy->id }} ? null : {{ $policy->id }})"
                        class="flex w-full items-center justify-between gap-3 px-5 py-4 text-left">
                        <span class="flex items-center gap-2 font-medium text-secondary-dark">
                            <x-icon name="shield" class="w-4 h-4 text-primary" />
                            {{ $policy->title }}
                        </span>
                        <x-icon name="chevron-down" class="w-4 h-4 text-secondary transition" x-bind:class="open === {{ $policy->id }} && '-rotate-180'" />
                    </button>

                    <div x-show="open === {{ $policy->id }}" x-cloak class="border-t border-app-border px-5 py-5">
                        <p class="text-sm text-secondary-dark whitespace-pre-line">{{ $policy->content ?: 'No content has been added for this policy yet.' }}</p>

                        <form method="POST" action="{{ route('client.projects.policies.destroy', ['project' => $project, 'policy' => $policy]) }}" class="mt-4" onsubmit="return confirm('Remove this policy?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-medium text-danger hover:underline">
                                <x-icon name="trash" class="w-3.5 h-3.5" />
                                Remove
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-app-border bg-white p-8 text-center text-sm text-secondary">
                    You haven't added any {{ $brandLabel }}-specific policies yet.
                </div>
            @endforelse
        </div>

        <div class="mt-5 rounded-xl border border-app-border bg-white p-5">
            <h3 class="text-sm font-semibold text-secondary-dark">Add a policy for {{ $brandLabel }}</h3>
            <form method="POST" action="{{ route('client.projects.policies.store', $project) }}" class="mt-3 space-y-3">
                @csrf
                <div>
                    <x-input-label for="policy_title" value="Title *" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="policy_title" name="title" type="text" class="mt-1.5 w-full" placeholder="e.g. Comment Moderation Policy" :value="old('title')" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="policy_content" value="Details" class="text-xs uppercase tracking-wide" />
                    <textarea id="policy_content" name="content" rows="6"
                        class="mt-1.5 w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">{{ old('content') }}</textarea>
                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                </div>
                <x-primary-button>Add Policy</x-primary-button>
            </form>
        </div>
    </div>
</div>
