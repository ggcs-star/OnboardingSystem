<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Product;
use Illuminate\View\View;

class ProjectTrainingOverviewController extends Controller
{
    public function index(): View
    {
        $clients = Client::with('trainingProgress')->orderBy('company_name')->get();

        $products = Product::with(['training', 'projects.client'])
            ->orderBy('name')
            ->get();

        $products->each(function (Product $product) use ($clients) {
            $trainingIds = $product->training->pluck('id');
            $total = $trainingIds->count();

            $projectsByClient = $product->projects->groupBy('client_id');

            $clientRows = $clients->map(function (Client $client) use ($trainingIds, $total, $projectsByClient) {
                $done = $client->trainingProgress
                    ->whereIn('training_id', $trainingIds)
                    ->where('completed', true)
                    ->count();

                $projects = $projectsByClient->get($client->id);

                return (object) [
                    'client' => $client,
                    'onboarded' => (bool) $projects,
                    'projectNames' => $projects?->pluck('project_name') ?? collect(),
                    'done' => $done,
                    'total' => $total,
                ];
            })->filter(fn ($row) => $row->done > 0)->values();

            $product->setRelation('clientRows', $clientRows);
        });

        return view('admin.training.index', ['products' => $products]);
    }
}
