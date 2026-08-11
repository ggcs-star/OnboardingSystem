@php
    $project = $ticket->project;
    $brandLabel = $project->brand_name ?? $project->project_name;
    $statusBadge = support_ticket_status_badge($ticket->status);
    $categoryBadge = support_ticket_category_badge($ticket->category);
    $clientUserId = $project->client->user_id;
@endphp

<x-client-layout title="Support">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('client.support.index') }}" class="hover:text-primary">Support</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">{{ $ticket->ticket_no }}</span>
    </nav>

    <div class="mt-3 flex flex-wrap items-center gap-3">
        <h1 class="text-xl font-semibold text-secondary-dark">{{ $ticket->ticket_no }}</h1>
        <x-badge :classes="$categoryBadge['classes']" class="whitespace-nowrap">{{ $categoryBadge['label'] }}</x-badge>
        <x-badge :classes="$statusBadge['classes']" dot>{{ $statusBadge['label'] }}</x-badge>
    </div>
    <p class="text-sm text-secondary">{{ $project->product->name }} · {{ $brandLabel }}</p>

    <div class="mt-6 rounded-xl border border-app-border bg-white p-6">
        <div class="space-y-5">
            @foreach ($ticket->messages as $message)
                @php $isMine = $message->sender_id === $clientUserId; @endphp
                <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-lg rounded-xl px-4 py-3 text-sm {{ $isMine ? 'bg-primary text-white' : 'bg-surface-alt text-secondary-dark' }}">
                        <p class="whitespace-pre-line">{{ $message->message }}</p>
                        <p class="mt-1.5 text-xs {{ $isMine ? 'text-white/70' : 'text-secondary' }}">
                            {{ $isMine ? 'You' : ($message->sender->name ?? 'Support Team') }} · {{ $message->created_at->format('d-M-Y g:i A') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        @if (! in_array($ticket->status, ['resolved', 'closed'], true))
            <form method="POST" action="{{ route('client.support.messages.store', $ticket) }}" class="mt-6 space-y-3 border-t border-app-border pt-6">
                @csrf
                <textarea name="message" rows="3" placeholder="Write a reply..."
                    class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary" required>{{ old('message') }}</textarea>
                <x-input-error :messages="$errors->get('message')" />
                <div class="flex justify-end">
                    <x-primary-button>Send Reply</x-primary-button>
                </div>
            </form>
        @else
            <p class="mt-6 border-t border-app-border pt-6 text-center text-sm text-secondary">This ticket is {{ strtolower($statusBadge['label']) }} and no longer accepting replies.</p>
        @endif
    </div>
</x-client-layout>
