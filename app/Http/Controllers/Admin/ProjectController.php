<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Project;
use App\Models\SalesEmployee;
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
        $projects = Project::with(['client', 'product'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($q) use ($search) {
                    $q->where('project_name', 'like', "%{$search}%")
                        ->orWhere('brand_name', 'like', "%{$search}%")
                        ->orWhereHas('client', fn ($c) => $c->where('company_name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('client'), fn ($query) => $query->where('client_id', $request->integer('client')))
            ->when($request->filled('product'), fn ($query) => $query->where('product_id', $request->integer('product')))
            ->when($request->filled('stage'), fn ($query) => $query->where('current_stage', $request->string('stage')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.projects.index', [
            'projects' => $projects,
            'products' => Product::where('active', true)->orderBy('name')->get(),
            'filterClient' => $request->integer('client') ?: null,
        ]);
    }

    public function show(Project $project): View
    {
        $project->load(['client', 'product', 'salesEmployee', 'renewal.history']);

        $entries = $project->documentEntries();
        $statusCounts = $entries->countBy('status');

        [$docsDone, $docsTotal] = $project->documentsProgress();
        [$videosDone, $videosTotal] = $project->trainingProgressCount();

        $overallTotal = $docsTotal + $videosTotal;
        $overallPct = $overallTotal > 0 ? (int) round((($docsDone + $videosDone) / $overallTotal) * 100) : 0;

        $groups = $entries->groupBy('group_slug')->map(function ($groupEntries, $slug) {
            $total = $groupEntries->count();
            $submitted = $groupEntries->whereIn('status', ['submitted', 'approved'])->count();

            return (object) [
                'slug' => $slug,
                'label' => $groupEntries->first()->group_label,
                'mandatory' => $groupEntries->first()->group_mandatory,
                'total' => $total,
                'submitted' => $submitted,
            ];
        })->values();

        return view('admin.projects.show', [
            'project' => $project,
            'docsDone' => $docsDone,
            'docsTotal' => $docsTotal,
            'videosDone' => $videosDone,
            'videosTotal' => $videosTotal,
            'overallPct' => $overallPct,
            'pendingCount' => $statusCounts->get('pending', 0),
            'submittedCount' => $statusCounts->get('submitted', 0),
            'approvedCount' => $statusCounts->get('approved', 0),
            'rejectedCount' => $statusCounts->get('rejected', 0),
            'groups' => $groups,
            'renewalStatus' => $this->renewalService->statusFor($project->renewal),
            'salesEmployees' => SalesEmployee::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function updateStage(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'stage' => ['required', Rule::in(array_keys(Project::STAGES))],
        ]);

        $this->projectService->advanceStage($project, $data['stage']);

        return back()->with('success', 'Project stage updated.');
    }

    public function updateStatus(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Project::STATUSES))],
        ]);

        $this->projectService->updateStatus($project, $data['status']);

        return back()->with('success', 'Project status updated.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        if ($project->status === 'active') {
            return back()->with('error', 'This project is active — set it to On Hold or Inactive before deleting.');
        }

        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted.');
    }
}
