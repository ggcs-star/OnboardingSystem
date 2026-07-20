<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\RenewalService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(private RenewalService $renewalService)
    {
    }

    public function index(Request $request): View
    {
        $client = $request->user()->client;

        $projects = $client
            ? $client->projects()->with(['product.training', 'documentValues', 'client.trainingProgress'])->latest()->get()
            : collect();

        return view('client.projects.index', ['projects' => $projects]);
    }

    public function show(Request $request, Project $project): View
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);

        $tabs = [
            'documents' => ['label' => 'Documents', 'icon' => 'file-text'],
            'training' => ['label' => 'Training', 'icon' => 'video'],
            'policies' => ['label' => 'Policies', 'icon' => 'shield'],
            'renewal' => ['label' => 'Renewal', 'icon' => 'refresh-cw'],
        ];

        $activeTab = $request->query('tab', 'documents');
        if (! array_key_exists($activeTab, $tabs)) {
            $activeTab = 'documents';
        }

        $project->load([
            'product.policies',
            'product.training',
            'documentValues.documentField',
            'client.trainingProgress',
            'renewal',
        ]);

        return view('client.projects.show', [
            'project' => $project,
            'tabs' => $tabs,
            'activeTab' => $activeTab,
            'renewalStatus' => $this->renewalService->statusFor($project->renewal),
        ]);
    }
}
