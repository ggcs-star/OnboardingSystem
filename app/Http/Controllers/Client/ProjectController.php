<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProjectService;
use App\Services\RenewalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(
        private RenewalService $renewalService,
        private ProjectService $projectService,
    ) {
    }

    public function index(Request $request): View
    {
        $client = $request->user()->client;

        $products = $client
            ? $client->projects()->with('product')->get()->pluck('product')->unique('id')->sortBy('name')->values()
            : collect();

        $projects = $client
            ? $client->projects()->with(['product', 'salesEmployee'])
                ->when($request->filled('search'), function ($query) use ($request) {
                    $search = $request->string('search');
                    $query->where(function ($q) use ($search) {
                        $q->where('project_name', 'like', "%{$search}%")
                            ->orWhere('brand_name', 'like', "%{$search}%");
                    });
                })
                ->when($request->filled('product'), fn ($query) => $query->where('product_id', $request->integer('product')))
                ->when($request->filled('stage'), fn ($query) => $query->where('current_stage', $request->string('stage')))
                ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
                ->latest()
                ->get()
            : collect();

        return view('client.projects.index', ['projects' => $projects, 'products' => $products]);
    }

    public function show(Request $request, Project $project): View
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);

        $project->load([
            'product.policies',
            'policies',
            'renewal.history',
            'salesEmployee',
        ]);

        return view('client.projects.show', [
            'project' => $project,
            'renewalStatus' => $this->renewalService->statusFor($project->renewal),
            'readOnly' => $request->boolean('readonly'),
        ]);
    }

    public function documents(Request $request, Project $project): View
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);

        return view('client.projects.documents', ['project' => $project]);
    }

    public function toggleHold(Request $request, Project $project): RedirectResponse
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);

        $this->projectService->toggleBlocked($project);

        return redirect()->back();
    }

    public function updateContact(Request $request, Project $project): RedirectResponse
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
        ]);

        $project->update($data);

        return redirect()->back()->with('success', 'Contact details updated.');
    }
}
