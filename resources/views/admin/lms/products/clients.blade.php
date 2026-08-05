<x-admin-layout :title="$lmsProduct->name . ' — Clients With Access'">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.lms.products.index') }}" class="hover:text-primary">Documentation</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <a href="{{ route('admin.lms.products.show', $lmsProduct) }}" class="hover:text-primary">{{ $lmsProduct->name }}</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">Clients With Access</span>
    </nav>

    <div class="mt-3 flex items-start gap-4">
        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
            <x-icon name="book-open" class="w-6 h-6" />
        </span>
        <div>
            <h1 class="text-xl font-semibold text-secondary-dark">{{ $lmsProduct->name }}</h1>
            @if ($lmsProduct->product)
                <p class="text-sm text-secondary">
                    Access is automatic: every client with an active "{{ $lmsProduct->product->name }}" purchase can read this documentation on the client portal.
                    Manage purchases from <a href="{{ route('admin.clients.index') }}" class="text-primary hover:underline">Clients</a>.
                </p>
            @else
                <p class="text-sm text-danger">This documentation isn't linked to a SaaS product, so no client can access it yet. Edit the product to link it.</p>
            @endif
        </div>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl border border-app-border bg-white">
        <table class="min-w-full divide-y divide-app-border text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-secondary">
                    <th class="px-4 py-3">Client</th>
                    <th class="px-4 py-3">Email</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-app-border">
                @forelse ($assignedClients as $client)
                    <tr>
                        <td class="px-4 py-3 font-medium text-secondary-dark">{{ $client->company_name }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $client->user->email ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-10 text-center text-sm text-secondary">
                            No clients have an active purchase of this product yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
