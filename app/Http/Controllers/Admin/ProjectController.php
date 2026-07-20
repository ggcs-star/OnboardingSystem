<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectRequest;
use App\Http\Requests\Admin\UpdateProjectRequest;
use App\Models\Client;
use App\Models\Product;
use App\Models\Project;
use App\Services\ProjectService;
use App\Services\RenewalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectService $projectService,
        private RenewalService $renewalService,
    ) {
    }

    public function index(Request $request): View
    {
        $projects = Project::with(['product.documentFields', 'product.training', 'client.trainingProgress', 'documentValues', 'renewal', 'tickets'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($q) use ($search) {
                    $q->where('project_name', 'like', "%{$search}%")
                        ->orWhereHas('client', fn ($c) => $c->where('company_name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('product'), fn ($query) => $query->where('product_id', $request->integer('product')))
            ->when($request->filled('client'), fn ($query) => $query->where('client_id', $request->integer('client')))
            ->when($request->filled('stage'), fn ($query) => $query->where('current_stage', $request->string('stage')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.projects.index', [
            'projects' => $projects,
            'stats' => $this->projectService->getDashboardStats(),
            'products' => Product::orderBy('name')->get(),
            'clients' => Client::orderBy('company_name')->get(),
        ]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = $this->projectService->createProject($request->validated());

        return redirect()->route('admin.projects.show', $project)->with('success', 'Project created successfully.');
    }

    public function show(Project $project): View
    {
        $project->load([
            'product.documentFields', 'product.training', 'product.policies',
            'client.trainingProgress', 'documentValues.documentField',
            'timeline', 'renewal', 'tickets', 'customizationRequests.createdBy',
        ]);

        return view('admin.projects.show', [
            'project' => $project,
            'renewalStatus' => $this->renewalService->statusFor($project->renewal),
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()->route('admin.projects.show', $project)->with('success', 'Project updated.');
    }

    public function updateStage(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'stage' => ['required', Rule::in(array_keys(Project::STAGES))],
        ]);

        $this->projectService->advanceStage($project, $request->string('stage')->value());

        return redirect()->route('admin.projects.show', $project)->with('success', 'Project stage updated.');
    }

    public function toggleBlocked(Project $project): RedirectResponse
    {
        $this->projectService->toggleBlocked($project);

        return redirect()->route('admin.projects.show', $project)->with('success', 'Project status updated.');
    }
}
