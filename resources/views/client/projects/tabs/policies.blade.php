<div class="space-y-4" x-data="{ open: {{ $project->product->policies->first()?->id ?? 'null' }} }">
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
            No policies published yet.
        </div>
    @endforelse
</div>
