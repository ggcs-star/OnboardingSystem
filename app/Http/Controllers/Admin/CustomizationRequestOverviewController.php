<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class CustomizationRequestOverviewController extends Controller
{
    public function index(): View
    {
        $products = Product::with(['projects.client', 'projects.customizationRequests'])
            ->orderBy('name')
            ->get();

        $products->each(function (Product $product) {
            $rows = $product->projects
                ->filter(fn ($project) => $project->customizationRequests->isNotEmpty())
                ->flatMap(function ($project) {
                    return $project->customizationRequests->map(fn ($request) => (object) [
                        'request' => $request,
                        'project' => $project,
                        'client' => $project->client,
                    ]);
                })
                ->sortByDesc(fn ($row) => $row->request->created_at)
                ->values();

            $product->setRelation('customizationRows', $rows);
        });

        $products = $products->filter(fn (Product $product) => $product->customizationRows->isNotEmpty())->values();

        return view('admin.customization-requests.index', ['products' => $products]);
    }
}
