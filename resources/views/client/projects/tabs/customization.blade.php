<div class="space-y-6">
    <div class="rounded-xl border border-app-border bg-white p-5">
        <h3 class="font-medium text-secondary-dark">Request a Customization</h3>
        <p class="mt-1 text-sm text-secondary">Tell us what features or changes you'd like added to your project. Our team will review it and let you know what's possible.</p>

        <form method="POST" action="{{ route('client.projects.customization-requests.store', $project) }}" class="mt-4 space-y-4">
            @csrf

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

            <x-primary-button>Submit Request</x-primary-button>
        </form>
    </div>

    <div class="space-y-4">
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
            <div class="rounded-xl border border-dashed border-app-border bg-white p-8 text-center text-sm text-secondary">
                You haven't requested any customizations yet.
            </div>
        @endforelse
    </div>
</div>
