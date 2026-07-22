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

            <form method="POST" action="{{ route('client.onboarding.subscription.sales-employee', $project) }}" class="mt-4">
                @csrf
                <input type="hidden" name="sales_employee_id" value="">
                <button type="submit" class="text-xs text-secondary hover:text-danger hover:underline">Clear selection</button>
            </form>
        </div>
    @else
        <div class="rounded-xl border border-dashed border-app-border bg-white p-8 text-center text-sm text-secondary">
            No salesperson has been attributed to {{ $brandLabel }} yet.
        </div>
    @endif

    @if ($salesEmployees->isNotEmpty())
        <div>
            <h2 class="text-sm font-semibold uppercase tracking-wide text-secondary">{{ $project->salesEmployee ? 'Change Salesperson' : 'Select a Salesperson' }}</h2>
            <p class="mt-1 text-sm text-secondary">Let us know which salesperson you bought {{ $project->product->name }} ({{ $brandLabel }}) through.</p>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($salesEmployees as $salesEmployee)
                    @php $isSelected = $project->sales_employee_id === $salesEmployee->id; @endphp
                    <div class="flex flex-col rounded-xl border bg-white p-5 {{ $isSelected ? 'border-primary ring-1 ring-primary' : 'border-app-border' }}">
                        <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-primary-light text-primary">
                            <x-icon name="briefcase" class="w-5 h-5" />
                        </span>

                        <div class="mt-4 flex items-center gap-2">
                            <h3 class="text-base font-semibold text-secondary-dark">{{ $salesEmployee->name }}</h3>
                            @if ($isSelected)
                                <x-badge classes="bg-primary-light text-primary">Selected</x-badge>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-secondary">{{ $salesEmployee->phone }}</p>
                        @if ($salesEmployee->email)
                            <p class="text-sm text-secondary">{{ $salesEmployee->email }}</p>
                        @endif

                        <form method="POST" action="{{ route('client.onboarding.subscription.sales-employee', $project) }}" class="mt-5">
                            @csrf
                            <input type="hidden" name="sales_employee_id" value="{{ $salesEmployee->id }}">
                            <button type="submit" @disabled($isSelected)
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium
                                {{ $isSelected ? 'cursor-default bg-surface-alt text-secondary' : 'bg-primary text-white hover:bg-primary-dark' }}">
                                {{ $isSelected ? 'Selected' : 'Select' }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
