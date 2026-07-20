<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ProjectDocumentValue;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $client = $request->user()->client;

        $projects = $client
            ? $client->projects()->with(['product.training', 'documentValues', 'client.trainingProgress', 'tickets'])->latest()->get()
            : collect();

        $docsDone = 0;
        $docsTotal = 0;
        $trainingDone = 0;
        $trainingTotal = 0;

        foreach ($projects as $project) {
            [$done, $total] = $project->documentsProgress();
            $docsDone += $done;
            $docsTotal += $total;

            [$tDone, $tTotal] = $project->trainingProgressCount();
            $trainingDone += $tDone;
            $trainingTotal += $tTotal;
        }

        $documentStatusCounts = collect(ProjectDocumentValue::STATUSES)->map(
            fn ($status) => [
                'status' => $status,
                'count' => $projects->flatMap->documentValues->where('status', $status)->count(),
            ]
        );

        return view('client.dashboard', [
            'projects' => $projects,
            'documentStatusCounts' => $documentStatusCounts,
            'stats' => [
                'total_projects' => $projects->count(),
                'active_projects' => $projects->where('status', 'active')->count(),
                'docs_done' => $docsDone,
                'docs_total' => $docsTotal,
                'training_done' => $trainingDone,
                'training_total' => $trainingTotal,
                'open_tickets' => $projects->flatMap->tickets->where('status', 'open')->count(),
            ],
        ]);
    }
}
