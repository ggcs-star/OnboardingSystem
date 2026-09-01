<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class DocumentReviewController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::with(['client', 'product'])->get();

        $allEntries = $projects->flatMap(function (Project $project) {
            return $project->documentEntries()->map(function ($entry) use ($project) {
                $entry->project = $project;

                return $entry;
            });
        });

        $entries = $allEntries
            ->when($request->filled('search'), function ($collection) use ($request) {
                $search = mb_strtolower($request->string('search'));

                return $collection->filter(function ($entry) use ($search) {
                    return str_contains(mb_strtolower($entry->project->project_name), $search)
                        || str_contains(mb_strtolower($entry->project->client->company_name), $search)
                        || str_contains(mb_strtolower($entry->label), $search);
                });
            })
            ->when($request->filled('product'), fn ($collection) => $collection->filter(
                fn ($entry) => $entry->project->product_id === $request->integer('product')
            ))
            ->when($request->filled('status'), fn ($collection) => $collection->filter(
                fn ($entry) => $entry->status === $request->string('status')->value()
            ));

        $clients = $entries
            ->groupBy(fn ($entry) => $entry->project->client_id)
            ->map(function ($clientEntries) {
                $client = $clientEntries->first()->project->client;

                $products = $clientEntries
                    ->groupBy(fn ($entry) => $entry->project->product_id)
                    ->map(function ($productEntries) {
                        $product = $productEntries->first()->project->product;

                        $brands = $productEntries
                            ->groupBy(fn ($entry) => $entry->project->id)
                            ->map(function ($projectEntries) {
                                $project = $projectEntries->first()->project;

                                $groups = $projectEntries
                                    ->groupBy('group_slug')
                                    ->map(fn ($groupEntries) => (object) [
                                        'label' => $groupEntries->first()->group_label,
                                        'mandatory' => $groupEntries->first()->group_mandatory,
                                        'entries' => $groupEntries->values(),
                                        'pending' => $groupEntries->whereIn('status', ['pending', 'submitted', 'rejected'])->count(),
                                    ]);

                                return (object) [
                                    'project' => $project,
                                    'groups' => $groups,
                                    'pending' => $projectEntries->whereIn('status', ['pending', 'submitted', 'rejected'])->count(),
                                ];
                            })
                            ->values();

                        return (object) [
                            'product' => $product,
                            'brands' => $brands,
                            'pending' => $productEntries->whereIn('status', ['pending', 'submitted', 'rejected'])->count(),
                        ];
                    })
                    ->values();

                return (object) [
                    'client' => $client,
                    'products' => $products,
                    'pending' => $clientEntries->whereIn('status', ['pending', 'submitted', 'rejected'])->count(),
                ];
            })
            ->sortBy(fn ($row) => $row->client->company_name)
            ->values();

        $perPage = 10;
        $page = $request->integer('page', 1);

        $clientsPage = new LengthAwarePaginator(
            $clients->forPage($page, $perPage),
            $clients->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.documents.index', [
            'clients' => $clientsPage,
            'products' => Product::orderBy('name')->get(),
            'stats' => [
                'total_fields' => $allEntries->count(),
                'pending' => $allEntries->where('status', 'pending')->count(),
                'submitted' => $allEntries->where('status', 'submitted')->count(),
                'approved' => $allEntries->where('status', 'approved')->count(),
                'rejected' => $allEntries->where('status', 'rejected')->count(),
            ],
        ]);
    }
}
