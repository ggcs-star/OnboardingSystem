<x-admin-layout title="Product Inquiries">
    <!-- Header & Search Section -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">Product Inquiries</h1>
            <p class="mt-1 text-sm text-secondary">Manage and track all client inquiries for your products.</p>
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ url()->current() }}" class="flex w-full items-center gap-2 sm:w-auto">
            <div class="relative w-full sm:w-72">
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
            
            <button type="submit" class="shrink-0 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                Search
            </button>

            <!-- Clear Button (Only shows if something is searched) -->
            @if(request()->filled('search'))
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
                        <th scope="col" class="px-6 py-4">ID</th>
                        <th scope="col" class="px-6 py-4">Client Info</th>
                        <th scope="col" class="px-6 py-4">Product</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                        <th scope="col" class="px-6 py-4">Date</th>
                    </tr>
                </thead>
                
                <tbody class="divide-y divide-app-border bg-white">
                    @forelse($inquiries as $inquiry)
                        <tr class="transition-colors hover:bg-gray-50/50">
                            <!-- ID -->
                            <td class="whitespace-nowrap px-6 py-4 font-medium text-secondary">
                                #{{ $inquiry->id }}
                            </td>

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
                                </div>
                            </td>

                            <!-- Product -->
                            <td class="px-6 py-4 font-medium text-secondary-dark">
                                {{ optional($inquiry->product)->name ?? 'N/A' }}
                            </td>

                            <!-- Changeable Status Dropdown -->
                            <td class="whitespace-nowrap px-6 py-4">
                                <form method="POST" action="{{ route('admin.inquiries.update-status', $inquiry->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select 
                                        name="status" 
                                        onchange="this.form.submit()" 
                                        class="cursor-pointer rounded-lg border px-3 py-1.5 text-xs font-semibold uppercase tracking-wide shadow-sm transition-colors focus:ring-2 focus:ring-offset-1 
                                        @if($inquiry->status == 'pending') 
                                            border-yellow-200 bg-yellow-50 text-yellow-700 focus:border-yellow-500 focus:ring-yellow-500
                                        @elseif($inquiry->status == 'approved') 
                                            border-green-200 bg-green-50 text-green-700 focus:border-green-500 focus:ring-green-500
                                        @else 
                                            border-red-200 bg-red-50 text-red-700 focus:border-red-500 focus:ring-red-500
                                        @endif"
                                    >
                                        <option value="pending" @selected($inquiry->status == 'pending')>Pending</option>
                                        <option value="approved" @selected($inquiry->status == 'approved')>Approved</option>
                                        <option value="rejected" @selected($inquiry->status == 'rejected')>Rejected</option>
                                    </select>
                                </form>
                            </td>

                            <!-- Date -->
                            <td class="whitespace-nowrap px-6 py-4 text-secondary">
                                {{ $inquiry->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-secondary">
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