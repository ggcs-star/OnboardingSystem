<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\SalesEmployee;
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

        $projects = $client
            ? $client->projects()->with('product')->latest()->get()
            : collect();

        $groupedProjects = $projects
            ->groupBy('product_id')
            ->map(fn ($productProjects) => [
                'product' => $productProjects->first()->product,
                'projects' => $productProjects,
            ])
            ->values();

        return view('client.projects.index', ['groupedProjects' => $groupedProjects]);
    }

    public function show(Request $request, Project $project): View
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);

        $tabs = [
            'documents' => ['label' => 'Documents', 'icon' => 'file-text'],
            'training' => ['label' => 'Training', 'icon' => 'video'],
            'policies' => ['label' => 'Policies', 'icon' => 'shield'],
            'renewal' => ['label' => 'Subscription', 'icon' => 'refresh-cw'],
            'sales-contact' => ['label' => 'Contact', 'icon' => 'briefcase'],
            'customization' => ['label' => 'Customization', 'icon' => 'settings'],
        ];

        $activeTab = $request->query('tab', 'documents');
        if (! array_key_exists($activeTab, $tabs)) {
            $activeTab = 'documents';
        }

        $project->load([
            'product.policies',
            'policies',
            'product.training',
            'client.trainingProgress',
            'renewal.history',
            'customizationRequests.reviewedBy',
            'salesEmployee',
        ]);

        return view('client.projects.show', [
            'project' => $project,
            'tabs' => $tabs,
            'activeTab' => $activeTab,
            'renewalStatus' => $this->renewalService->statusFor($project->renewal),
            'salesEmployees' => SalesEmployee::where('status', 'active')->orderBy('name')->get(),
        ]);
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
