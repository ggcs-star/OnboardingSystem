<x-admin-layout title="Support">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Support</h1>
        <p class="mt-1 text-sm text-secondary">See every support request clients have raised, grouped by product</p>
    </div>

    <div class="mt-6 space-y-6">
        @forelse ($products as $product)
            @php $openCount = $product->ticketRows->whereNotIn('ticket.status', ['resolved', 'closed'])->count(); @endphp
            <div class="overflow-hidden rounded-xl border border-app-border bg-white">
                <div class="flex items-center justify-between gap-3 border-b border-app-border bg-surface-alt px-5 py-3">
                    <div class="flex items-center gap-2">
                        <x-badge classes="bg-white text-secondary-dark">{{ $product->name }}</x-badge>
                        <span class="text-xs text-secondary">{{ $product->ticketRows->count() }} requests</span>
                    </div>
                    @if ($openCount > 0)
                        <x-badge classes="bg-warning-light text-warning">{{ $openCount }} open</x-badge>
                    @endif
                </div>

                <div class="divide-y divide-app-border">
                    @foreach ($product->ticketRows as $row)
                        @php
                            $statusBadge = support_ticket_status_badge($row->ticket->status);
                            $categoryBadge = support_ticket_category_badge($row->ticket->category);
                            $firstMessage = $row->ticket->messages->first();
                        @endphp
                        <a href="{{ route('admin.support.show', $row->ticket) }}" class="block p-5 hover:bg-surface-alt/60">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-medium text-secondary">{{ $row->ticket->ticket_no }}</span>
                                        <x-badge :classes="$categoryBadge['classes']">{{ $categoryBadge['label'] }}</x-badge>
                                    </div>
                                    <p class="mt-1 text-sm font-medium text-secondary-dark">{{ $row->client->company_name }} · {{ $row->project->brand_name ?? $row->project->project_name }}</p>
                                    <p class="mt-2 text-sm text-secondary">{{ Str::limit($firstMessage?->message, 160) }}</p>
                                    <p class="mt-2 text-xs text-secondary/70">Submitted {{ $row->ticket->created_at->format('d-M-Y') }}</p>
                                </div>
                                <x-badge :classes="$statusBadge['classes']" class="shrink-0">{{ $statusBadge['label'] }}</x-badge>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                No support requests yet.
            </div>
        @endforelse
    </div>
</x-admin-layout>
