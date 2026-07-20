<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class ProjectTrainingOverviewController extends Controller
{
    public function index(): View
    {
        $products = Product::with(['training', 'projects.client.trainingProgress'])
            ->orderBy('name')
            ->get();

        $products->each(function (Product $product) {
            $trainingIds = $product->training->pluck('id');
            $total = $trainingIds->count();

            $clientRows = $product->projects
                ->groupBy('client_id')
                ->map(function ($projects) use ($trainingIds, $total) {
                    $client = $projects->first()->client;
                    $done = $client->trainingProgress
                        ->whereIn('training_id', $trainingIds)
                        ->where('completed', true)
                        ->count();

                    return (object) [
                        'client' => $client,
                        'firstProject' => $projects->first(),
                        'projectNames' => $projects->pluck('project_name'),
                        'done' => $done,
                        'total' => $total,
                    ];
                })
                ->values();

            $product->setRelation('clientRows', $clientRows);
        });

        return view('admin.training.index', ['products' => $products]);
    }
}
