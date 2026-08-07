<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
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
        $client = $request->user()->client;

        $projects = $client
            ? $client->projects()->with(['product', 'tickets.messages'])->latest()->get()
            : collect();

        $tickets = SupportTicket::with(['project.product', 'project.client', 'messages'])
            ->whereHas('project', fn ($query) => $query->where('client_id', $client?->id ?? 0))
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('ticket_no', 'like', '%' . $request->string('search') . '%');
            })
            ->when($request->filled('product'), function ($query) use ($request) {
                $query->whereHas('project', fn ($p) => $p->where('product_id', $request->product));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $products = $client
            ? $client->products()->wherePivot('status', true)->orderBy('name')->get()
            : collect();

        return view('client.support.index', [
            'projects' => $projects,
            'tickets' => $tickets,
            'products' => $products,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $client = $request->user()->client;

        abort_unless($client, 403);

        $validated = $request->validate([
            'project_id' => ['required', Rule::exists('projects', 'id')->where('client_id', $client->id)],
            'category' => ['required', Rule::in(array_keys(SupportTicket::CATEGORIES))],
            'description' => ['required', 'string'],
        ]);

        $project = Project::findOrFail($validated['project_id']);

        $this->supportTicketService->createTicket($project, $request->user()->id, $validated);

        return back()->with('success', 'Support request submitted.');
    }

    public function show(Request $request, SupportTicket $supportTicket): View
    {
        abort_unless($supportTicket->project->client->user_id === $request->user()->id, 403);

        $supportTicket->load(['project.product', 'messages.sender']);

        return view('client.support.show', ['ticket' => $supportTicket]);
    }

    public function reply(Request $request, SupportTicket $supportTicket): RedirectResponse
    {
        abort_unless($supportTicket->project->client->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'message' => ['required', 'string'],
        ]);

        $this->supportTicketService->addMessage($supportTicket, $request->user()->id, $data['message']);

        return back()->with('success', 'Reply sent.');
    }
}
