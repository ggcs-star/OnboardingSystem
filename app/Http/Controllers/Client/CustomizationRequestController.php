<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreCustomizationRequestRequest;
use App\Models\CustomizationRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomizationRequestController extends Controller
{
    public function index(Request $request): View
    {
        $client = $request->user()->client;

        $projects = $client
            ? $client->projects()->with(['product', 'customizationRequests'])->latest()->get()
            : collect();

        $requests = CustomizationRequest::with(['project.product', 'project.client'])
            ->whereHas('project', fn ($query) => $query->where('client_id', $client?->id ?? 0))
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->string('search') . '%');
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

        return view('client.customization-requests.index', [
            'projects' => $projects,
            'requests' => $requests,
            'products' => $products,
        ]);
    }

    public function store(StoreCustomizationRequestRequest $request, Project $project): RedirectResponse
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);

        $this->createRequest($project, $request->user()->id, $request->validated());

        return back()->with('success', 'Customization request submitted.');
    }

    public function storeAny(Request $request): RedirectResponse
    {
        $client = $request->user()->client;

        abort_unless($client, 403);

        $validated = $request->validate([
            'project_id' => ['required', Rule::exists('projects', 'id')->where('client_id', $client->id)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        $project = Project::findOrFail($validated['project_id']);

        $this->createRequest($project, $request->user()->id, [
            'title' => $validated['title'],
            'description' => $validated['description'],
        ]);

        return back()->with('success', 'Customization request submitted.');
    }

    private function createRequest(Project $project, int $userId, array $data): void
    {
        $project->customizationRequests()->create([
            ...$data,
            'created_by' => $userId,
            'status' => 'pending',
        ]);
    }
}
