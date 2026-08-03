@php
    $brandLabel = $project->brand_name ?? $project->project_name;
    $client = $project->client;
@endphp

<div class="space-y-6">
    <div class="rounded-xl border border-app-border bg-white p-6" x-data="{ editing: false }">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-secondary">Your Contact Details for {{ $brandLabel }}</p>
                <p class="mt-1 text-xs text-secondary">This is the contact information our team has on file for {{ $brandLabel }} specifically — it can be different from your account's default contact.</p>
            </div>
            <button type="button" x-on:click="editing = !editing" class="shrink-0 text-xs font-medium text-primary hover:underline" x-text="editing ? 'Cancel' : 'Edit'"></button>
        </div>

        <div class="mt-4 flex items-center gap-3" x-show="!editing">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                <x-icon name="phone" class="w-5 h-5" />
            </span>
            <div>
                <p class="text-base font-semibold text-secondary-dark">{{ $project->contactName() }}</p>
                <p class="text-sm text-secondary">{{ $project->contactPhone() ?: 'No contact number on file' }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('client.projects.contact.update', $project) }}" x-show="editing" x-cloak class="mt-4 space-y-4">
            @csrf
            @method('PATCH')
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="contact_name" value="Contact Name" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="contact_name" name="contact_name" class="mt-1.5" :value="old('contact_name', $project->contact_name)" placeholder="{{ $client->owner_name ?: $client->company_name }}" />
                    <x-input-error :messages="$errors->get('contact_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="contact_phone" value="Contact Number" class="text-xs uppercase tracking-wide" />
                    <x-text-input id="contact_phone" name="contact_phone" class="mt-1.5" :value="old('contact_phone', $project->contact_phone)" placeholder="{{ $client->phone ?: 'e.g. 9876543210' }}" />
                    <x-input-error :messages="$errors->get('contact_phone')" class="mt-2" />
                </div>
            </div>
            <x-primary-button>Save Contact Details</x-primary-button>
        </form>
    </div>

    @if ($project->salesEmployee)
        <div class="rounded-xl border border-app-border bg-white p-6">
            <p class="text-xs font-semibold uppercase tracking-wide text-secondary">Your Sales Contact for {{ $brandLabel }}</p>
            <div class="mt-4 flex items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                    <x-icon name="briefcase" class="w-5 h-5" />
                </span>
                <div>
                    <p class="text-base font-semibold text-secondary-dark">{{ $project->salesEmployee->name }}</p>
                    <p class="text-sm text-secondary">
                        {{ $project->salesEmployee->phone }}
                        @if ($project->salesEmployee->email)
                            · {{ $project->salesEmployee->email }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="rounded-xl border border-dashed border-app-border bg-white p-8 text-center text-sm text-secondary">
            No salesperson has been attributed to {{ $brandLabel }} yet.
        </div>
    @endif
</div>
