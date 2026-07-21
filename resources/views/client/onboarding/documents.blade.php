<x-client-layout title="Onboarding">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Onboarding — {{ $project->product->name }}</h1>
        <p class="mt-1 text-sm text-secondary">Upload the documents below to finish onboarding {{ $project->product->name }}.</p>
    </div>

    <x-onboarding-steps :current="2" class="mt-8" />

    @php
        [$mandatoryDone, $mandatoryTotal] = $mandatoryProgress;
        $docsComplete = $mandatoryDone === $mandatoryTotal;
    @endphp
    <div class="mt-8 flex items-start gap-3 rounded-xl border px-4 py-3 text-sm
        {{ $docsComplete ? 'border-success/20 bg-success-light text-success' : 'border-warning/20 bg-warning-light text-warning' }}">
        <x-icon :name="$docsComplete ? 'check' : 'alert'" class="mt-0.5 w-4 h-4 shrink-0" />
        <div>
            @if ($docsComplete)
                <p class="font-medium">All required documents are in!</p>
                <p class="mt-0.5 opacity-90">You can still fill in the optional details below anytime. Continue to set up your subscription next.</p>
            @else
                <p class="font-medium">{{ $mandatoryDone }}/{{ $mandatoryTotal }} required documents submitted</p>
                <p class="mt-0.5 opacity-90">Submit the remaining required documents below to complete onboarding for {{ $project->product->name }}.</p>
            @endif
        </div>
    </div>

    <div class="mt-6">
        @include('client.projects.tabs.documents', ['project' => $project])
    </div>

    <div class="mt-6 flex justify-end">
        <a href="{{ route('client.onboarding.subscription', $project) }}" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-dark">
            Continue to Subscription
        </a>
    </div>
</x-client-layout>
