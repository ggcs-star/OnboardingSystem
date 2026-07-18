@php $renewal = $project->renewal; @endphp

<div class="rounded-xl border border-app-border bg-white p-6">
    @if (! $renewal)
        <div class="text-center text-sm text-secondary">
            <x-icon name="refresh-cw" class="mx-auto mb-3 h-8 w-8 text-secondary/50" />
            Your renewal details will appear here once your project goes live.
        </div>
    @else
        @php $badge = renewal_status_badge($renewalStatus); @endphp

        @if ($renewalStatus === 'expiring')
            <div class="mb-4 flex items-center gap-2 rounded-lg bg-warning-light px-4 py-3 text-sm text-warning">
                <x-icon name="alert" class="w-4 h-4" />
                Your plan is expiring soon on {{ $renewal->expiry_date->format('d-M-Y') }}. Please contact us to renew.
            </div>
        @elseif ($renewalStatus === 'expired')
            <div class="mb-4 flex items-center gap-2 rounded-lg bg-danger-light px-4 py-3 text-sm text-danger">
                <x-icon name="alert" class="w-4 h-4" />
                Your plan expired on {{ $renewal->expiry_date->format('d-M-Y') }}. Please contact us to renew.
            </div>
        @endif

        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-secondary-dark">{{ $renewal->plan_name }}</h2>
            <x-badge :classes="$badge['classes']" dot>{{ $badge['label'] }}</x-badge>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-5 sm:grid-cols-3">
            <div>
                <p class="text-xs uppercase tracking-wide text-secondary">Go Live</p>
                <p class="mt-1 font-medium text-secondary-dark">{{ $renewal->go_live_date?->format('d-M-Y') }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-secondary">Expiry</p>
                <p class="mt-1 font-medium text-secondary-dark">{{ $renewal->expiry_date?->format('d-M-Y') }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-secondary">Plan Duration</p>
                <p class="mt-1 font-medium text-secondary-dark">{{ $renewal->plan_duration_months }} months</p>
            </div>
        </div>
    @endif
</div>
