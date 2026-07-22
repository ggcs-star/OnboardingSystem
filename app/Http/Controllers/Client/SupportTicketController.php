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

        return view('client.support.index', ['projects' => $projects]);
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
