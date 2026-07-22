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

    public function index(): View
    {
        $products = Product::with(['projects.client', 'projects.tickets.messages'])
            ->orderBy('name')
            ->get();

        $products->each(function (Product $product) {
            $rows = $product->projects
                ->filter(fn ($project) => $project->tickets->isNotEmpty())
                ->flatMap(function ($project) {
                    return $project->tickets->map(fn ($ticket) => (object) [
                        'ticket' => $ticket,
                        'project' => $project,
                        'client' => $project->client,
                    ]);
                })
                ->sortByDesc(fn ($row) => $row->ticket->created_at)
                ->values();

            $product->setRelation('ticketRows', $rows);
        });

        $products = $products->filter(fn (Product $product) => $product->ticketRows->isNotEmpty())->values();

        return view('admin.support.index', ['products' => $products]);
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
