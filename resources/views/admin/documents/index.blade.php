<x-admin-layout title="Client Documents">
    <div>
        <h1 class="text-2xl font-semibold text-secondary-dark">Client Documents</h1>
        <p class="mt-1 text-sm text-secondary">Review and approve client document submissions, grouped by client, product and document group</p>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        <x-stat-card label="Total Fields" :value="$stats['total_fields']" />
        <x-stat-card label="Pending" :value="$stats['pending']" tint="warning" />
        <x-stat-card label="Submitted" :value="$stats['submitted']" />
        <x-stat-card label="Approved" :value="$stats['approved']" />
        <x-stat-card label="Rejected" :value="$stats['rejected']" tint="danger" />
    </div>

    <form method="GET" class="mt-6 flex flex-col gap-3 sm:flex-row">
        <div class="relative flex-1">
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-secondary" />
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search project, client, field..."
                class="w-full rounded-lg border-app-border pl-10 text-sm shadow-sm focus:border-primary focus:ring-primary">
        </div>
        <select name="product" onchange="this.form.submit()" class="rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
            <option value="">All Products</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected(request('product') == $product->id)>{{ $product->name }}</option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()" class="rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
            <option value="">All Status</option>
            @foreach (['pending', 'submitted', 'approved', 'rejected'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <button type="submit" class="hidden"></button>
    </form>

    <div class="mt-6 space-y-3">
        @forelse ($clients as $clientRow)
            <div class="rounded-xl border border-app-border bg-white" x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                <button type="button" x-on:click="open = !open" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-light text-xs font-semibold text-primary">
                            {{ Str::substr($clientRow->client->company_name, 0, 1) }}
                        </span>
                        <div>
                            <p class="font-medium text-secondary-dark">{{ $clientRow->client->company_name }}</p>
                            <p class="text-xs text-secondary">{{ $clientRow->products->count() }} product{{ $clientRow->products->count() === 1 ? '' : 's' }} onboarded</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @if ($clientRow->pending > 0)
                            <x-badge classes="bg-warning-light text-warning">{{ $clientRow->pending }} pending</x-badge>
                        @else
                            <x-badge classes="bg-success-light text-success">All clear</x-badge>
                        @endif
                        <x-icon name="chevron-down" class="w-4 h-4 shrink-0 text-secondary transition-transform" x-bind:class="open && 'rotate-180'" />
                    </div>
                </button>

                <div x-show="open" x-cloak class="space-y-3 border-t border-app-border p-4">
                    @include('admin.documents.partials.product-groups', ['products' => $clientRow->products])
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                No document submissions yet.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $clients->links() }}
    </div>
</x-admin-layout>
