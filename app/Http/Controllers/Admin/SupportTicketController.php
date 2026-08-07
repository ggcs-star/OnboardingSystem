<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SupportTicket;
use App\Services\SupportTicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function __construct(private SupportTicketService $supportTicketService)
    {
    }

    public function index(Request $request): View
    {
        $tickets = SupportTicket::with(['project.client', 'project.product', 'messages'])
            ->when($request->filled('product'), function ($query) use ($request) {
                $query->whereHas('project', fn ($p) => $p->where('product_id', $request->product));
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category', $request->category);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('ticket_no', 'like', "%{$search}%")
                        ->orWhereHas('project.client', fn ($c) => $c->where('company_name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('date_range'), function ($query) use ($request) {
                [$from, $to] = match ($request->date_range) {
                    'last_7_days' => [now()->subDays(7)->startOfDay(), now()->endOfDay()],
                    'last_30_days' => [now()->subDays(30)->startOfDay(), now()->endOfDay()],
                    'this_month' => [now()->startOfMonth(), now()->endOfMonth()],
                    'last_month' => [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()],
                    default => [null, null],
                };

                if ($from && $to) {
                    $query->whereBetween('created_at', [$from, $to]);
                }
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $products = Product::orderBy('name')->get();

        return view('admin.support.index', ['tickets' => $tickets, 'products' => $products]);
    }

    public function show(SupportTicket $supportTicket): View
    {
        $supportTicket->load(['project.product', 'project.client', 'messages.sender']);

        return view('admin.support.show', ['ticket' => $supportTicket]);
    }

    public function reply(Request $request, SupportTicket $supportTicket): RedirectResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string'],
        ]);

        $this->supportTicketService->addMessage($supportTicket, $request->user()->id, $data['message']);

        return back()->with('success', 'Reply sent.');
    }

    public function updateStatus(Request $request, SupportTicket $supportTicket): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(SupportTicket::STATUSES))],
        ]);

        $supportTicket->update($data);

        return back()->with('success', 'Ticket status updated.');
    }
}
