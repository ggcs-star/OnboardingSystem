@php $renewal = $project->renewal; @endphp

<div class="rounded-xl border border-app-border bg-white p-6">
    @if (! $renewal)
        <div class="text-center text-sm text-secondary">
            <x-icon name="refresh-cw" class="mx-auto mb-3 h-8 w-8 text-secondary/50" />
            A renewal record is created automatically once this project's stage reaches <strong>Live</strong>.
        </div>
    @else
        @php $badge = renewal_status_badge($renewalStatus); @endphp
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-secondary-dark">{{ $renewal->plan_name }}</h2>
            <x-badge :classes="$badge['classes']" dot>{{ $badge['label'] }}</x-badge>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-5 sm:grid-cols-4">
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
            <div>
                <p class="text-xs uppercase tracking-wide text-secondary">Renewal Amount</p>
                <p class="mt-1 font-medium text-secondary-dark">{{ $renewal->renewal_amount ? '₹' . number_format($renewal->renewal_amount, 2) : '—' }}</p>
            </div>
        </div>
    @endif
</div>
