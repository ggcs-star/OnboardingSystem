<x-admin-layout title="Product Inquiries">
    <!-- Header & Search Section -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">Product Inquiries</h1>
            <p class="mt-1 text-sm text-secondary">Manage and track all client inquiries for your products.</p>
        </div>

        <!-- Search & Filter Form -->
        <form method="GET" action="{{ url()->current() }}" class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
            <div class="relative w-full sm:w-64">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <x-icon name="search" class="h-4 w-4 text-gray-400" />
                </div>
                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search name, email, or company..."
                    class="block w-full rounded-lg border-gray-300 pl-10 text-sm shadow-sm transition-colors focus:border-primary focus:ring-primary"
                >
            </div>

            <select name="product" onchange="this.form.submit()"
                class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary sm:w-48">
                <option value="">All Products</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected(request('product') == $product->id)>{{ $product->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="shrink-0 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                Search
            </button>

            <!-- Clear Button (Only shows if something is searched/filtered) -->
            @if(request()->filled('search') || request()->filled('product'))
                <a href="{{ url()->current() }}" class="shrink-0 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Table Container -->
    <div class="overflow-hidden rounded-xl border border-app-border bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-app-border bg-gray-50 text-xs font-medium uppercase tracking-wide text-secondary">
                    <tr>
                        <th scope="col" class="px-6 py-4">Client Info</th>
                        <th scope="col" class="px-6 py-4">Product</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                        <th scope="col" class="px-6 py-4">Date</th>
                    </tr>
                </thead>
                
                <tbody class="divide-y divide-app-border bg-white">
                    @forelse($inquiries as $inquiry)
                        <tr class="transition-colors hover:bg-gray-50/50">
                            <!-- Combined Client Info -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-secondary-dark">
                                        {{ optional($inquiry->client)->company_name ?? $inquiry->company }}
                                    </span>
                                    <span class="mt-0.5 text-xs text-secondary">{{ $inquiry->contact_person }}</span>
                                    <div class="mt-1 flex items-center gap-2 text-xs text-secondary/80">
                                        <a href="mailto:{{ $inquiry->email }}" class="hover:text-primary hover:underline">{{ $inquiry->email }}</a>
                                        <span>•</span>
                                        <a href="tel:{{ $inquiry->phone }}" class="hover:text-primary hover:underline">{{ $inquiry->phone }}</a>
                                    </div>
                                    @if ($inquiry->message)
                                        <p class="mt-1.5 max-w-xs truncate text-xs text-secondary" title="{{ $inquiry->message }}">
                                            {{ $inquiry->message }}
                                        </p>
                                    @endif
                                </div>
                            </td>

                            <!-- Product -->
                            <td class="px-6 py-4 font-medium text-secondary-dark">
                                {{ optional($inquiry->product)->name ?? 'N/A' }}
                            </td>

                            <!-- Changeable Status Dropdown -->
                            <td class="whitespace-nowrap px-6 py-4">
                                @php
                                    $inquiryColorClass = match ($inquiry->status) {
                                        'approved' => 'text-success',
                                        'rejected' => 'text-danger',
                                        default => 'text-warning',
                                    };
                                    $inquirySelectClasses = match ($inquiry->status) {
                                        'approved' => 'bg-success-light text-success',
                                        'rejected' => 'bg-danger-light text-danger',
                                        default => 'bg-warning-light text-warning',
                                    };
                                @endphp
                                <form method="POST" action="{{ route('admin.inquiries.update-status', $inquiry->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="relative inline-block">
                                        <span class="pointer-events-none absolute left-3 top-1/2 h-1.5 w-1.5 -translate-y-1/2 rounded-full bg-current {{ $inquiryColorClass }}"></span>
                                        <select
                                            name="status"
                                            onchange="this.form.submit()"
                                            class="cursor-pointer appearance-none bg-none rounded-full border-0 py-1.5 pl-7 pr-7 text-xs font-medium shadow-sm transition-colors focus:ring-2 focus:ring-primary {{ $inquirySelectClasses }}"
                                        >
                                            <option value="pending" @selected($inquiry->status == 'pending')>Pending</option>
                                            <option value="approved" @selected($inquiry->status == 'approved')>Approved</option>
                                            <option value="rejected" @selected($inquiry->status == 'rejected')>Rejected</option>
                                        </select>
                                        <x-icon name="chevron-down"
                                            class="pointer-events-none absolute right-2.5 top-1/2 h-3 w-3 -translate-y-1/2 {{ $inquiryColorClass }}" />
                                    </div>
                                </form>
                            </td>

                            <!-- Date -->
                            <td class="whitespace-nowrap px-6 py-4 text-secondary">
                                {{ $inquiry->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-secondary">
                                <div class="flex flex-col items-center justify-center">
                                    <x-icon name="inbox" class="mb-3 h-12 w-12 text-gray-300" />
                                    @if(request()->filled('search'))
                                        <p class="text-base font-medium text-secondary-dark">No results found</p>
                                        <p class="mt-1">We couldn't find any inquiries matching "<strong>{{ request('search') }}</strong>".</p>
                                    @else
                                        <p class="text-base font-medium text-secondary-dark">No inquiries yet</p>
                                        <p class="mt-1">New product inquiries will appear here.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        @if($inquiries->hasPages())
            <div class="border-t border-app-border bg-gray-50 px-6 py-4">
                {{ $inquiries->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>