<x-admin-layout :title="$client->company_name">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.clients.index') }}" class="hover:text-primary">Clients</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">{{ $client->company_name }}</span>
    </nav>

    <div class="mt-4 max-w-2xl rounded-xl border border-app-border bg-white p-6">
        <div class="flex items-center gap-3">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary-light text-lg font-semibold text-primary">
                {{ Str::substr($client->company_name, 0, 1) }}
            </span>
            <div>
                <h1 class="text-lg font-semibold text-secondary-dark">{{ $client->company_name }}</h1>
                @php $statusBadge = $client->status === 'active' ? 'bg-success-light text-success' : 'bg-danger-light text-danger'; @endphp
                <x-badge :classes="$statusBadge" dot>{{ $client->status === 'active' ? 'Active' : 'Inactive' }}</x-badge>
            </div>
        </div>

        <div class="mt-6 overflow-hidden rounded-lg border border-app-border">
            <table class="w-full text-sm">
                <tbody class="divide-y divide-app-border">
                    <tr>
                        <th class="w-40 bg-surface-alt px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-secondary">Email</th>
                        <td class="px-4 py-2.5 text-secondary-dark">{{ $client->user?->email ?: '—' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-surface-alt px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-secondary">Owner</th>
                        <td class="px-4 py-2.5 text-secondary-dark">{{ $client->owner_name ?: '—' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-surface-alt px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-secondary">Phone</th>
                        <td class="px-4 py-2.5 text-secondary-dark">{{ $client->phone ?: '—' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-surface-alt px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-secondary">Address</th>
                        <td class="px-4 py-2.5 text-secondary-dark">{{ collect([$client->address, $client->city, $client->state, $client->country])->filter()->implode(', ') ?: '—' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-surface-alt px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-secondary">Onboarded</th>
                        <td class="px-4 py-2.5 text-secondary-dark">{{ $client->created_at->format('d-M-Y') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 max-w-2xl rounded-xl border border-app-border bg-white p-6">
        <h2 class="text-sm font-semibold text-secondary-dark">Assigned Products</h2>
        <p class="mt-1 text-xs text-secondary">Only the products onboarded for this client</p>

        @if ($client->products->isEmpty())
            <p class="mt-4 text-sm text-secondary">No products assigned yet.</p>
        @else
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($client->products as $product)
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-app-border bg-surface-alt px-3 py-1 text-xs font-medium text-secondary-dark">
                        <x-icon name="box" class="w-3.5 h-3.5 text-secondary" />
                        {{ $product->name }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>
</x-admin-layout>
