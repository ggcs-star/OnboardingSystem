<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Product;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private ProjectService $projectService)
    {
    }

    public function index(): View
    {
        $projects = Project::with(['product.training', 'client.trainingProgress'])->get();

        $stageCounts = collect(Project::STAGES)->map(
            fn ($label, $key) => ['label' => $label, 'value' => $projects->where('current_stage', $key)->count()]
        )->values();

        $productCounts = Product::withCount('projects')
            ->orderByDesc('projects_count')
            ->get()
            ->map(fn (Product $product) => ['label' => $product->name, 'value' => $product->projects_count])
            ->filter(fn ($row) => $row['value'] > 0)
            ->values();

        $documentStatusCounts = collect(Project::DOCUMENT_STATUSES)->map(
            fn ($status) => ['status' => $status, 'count' => $projects->flatMap->documentEntries()->where('status', $status)->count()]
        );

        $recentProjects = Project::with(['product.training', 'client.trainingProgress'])->latest()->take(6)->get();

        $trainingByClient = $projects
            ->groupBy(fn (Project $project) => $project->client->company_name)
            ->map(function ($clientProjects, $companyName) {
                $client = $clientProjects->first()->client;
                $trainingIds = $clientProjects->pluck('product.training')->flatten()->pluck('id')->unique();
                $total = $trainingIds->count();
                $done = $client->trainingProgress->whereIn('training_id', $trainingIds)->where('completed', true)->count();
                $pct = $total > 0 ? round($done / $total * 100) : 0;

                return ['label' => $companyName, 'value' => $pct, 'done' => $done, 'total' => $total];
            })
            ->filter(fn ($row) => $row['total'] > 0)
            ->sortBy('value')
            ->take(8)
            ->map(fn ($row) => [
                'label' => $row['label'],
                'value' => $row['value'],
                'hint' => $row['done'] . '/' . $row['total'] . ' videos watched',
                'color' => match (true) {
                    $row['value'] >= 100 => 'bg-success',
                    $row['value'] >= 50 => 'bg-warning',
                    default => 'bg-danger',
                },
            ])
            ->values();

        return view('admin.dashboard', [
            'productStats' => [
                'total' => Product::count(),
                'active' => Product::where('active', true)->count(),
            ],
            'clientStats' => [
                'total' => Client::count(),
                'active' => Client::where('status', 'active')->count(),
                'blocked' => Client::where('status', 'blocked')->count(),
            ],
            'projectStats' => $this->projectService->getDashboardStats(),
            'stageCounts' => $stageCounts,
            'productCounts' => $productCounts,
            'documentStatusCounts' => $documentStatusCounts,
            'recentProjects' => $recentProjects,
            'trainingByClient' => $trainingByClient,
        ]);
    }
}
