<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientRequest;
use App\Models\Client;
use App\Models\Project;
use App\Models\SalesEmployee;
use App\Services\ClientService;
use App\Services\RenewalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Product;
class ClientController extends Controller
{
    public function __construct(
        private ClientService $clientService,
        private RenewalService $renewalService,
    ) {
    }

    public function index(Request $request): View
    {
        $products = Product::where('active', true)
            ->orderBy('name')
            ->get();
        $clients = Client::with(['user', 'projects'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                        ->orWhere('owner_name', 'like', "%{$search}%")
                        ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.clients.index', [
            'clients' => $clients,
            'products' => $products,
        ]);
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $this->clientService->createClient($request->validated());

        return redirect()->route('admin.clients.index')->with('success', 'Client added successfully.');
    }

    public function show(Request $request, Client $client): View
    {
        $client->load(['user', 'projects.product', 'projects.renewal.history', 'projects.salesEmployee']);

        $tabs = ['overview', 'projects', 'documents'];
        $activeTab = $request->query('tab', 'overview');
        if (!in_array($activeTab, $tabs, true)) {
            $activeTab = 'overview';
        }

        $projectsByProduct = $client->projects
            ->groupBy('product_id')
            ->map(function ($productProjects) {
                $firstProject = $productProjects->first();
                [$videosDone, $videosTotal] = $firstProject->trainingProgressCount();

                return (object) [
                    'product' => $firstProject->product,
                    'videosDone' => $videosDone,
                    'videosTotal' => $videosTotal,
                    'projects' => $productProjects->map(function (Project $project) {
                        [$docsDone, $docsTotal] = $project->documentsProgress();

                        return (object) [
                            'project' => $project,
                            'docsDone' => $docsDone,
                            'docsTotal' => $docsTotal,
                        ];
                    })->values(),
                ];
            })
            ->values();

        $products = $client->projects
            ->groupBy('product_id')
            ->map(function ($productProjects) {
                $brands = $productProjects->map(function (Project $project) {
                    $groups = $project->documentEntries()
                        ->groupBy('group_slug')
                        ->map(fn($groupEntries) => (object) [
                            'label' => $groupEntries->first()->group_label,
                            'mandatory' => $groupEntries->first()->group_mandatory,
                            'entries' => $groupEntries->values()->map(function ($entry) use ($project) {
                                $entry->project = $project;

                                return $entry;
                            }),
                            'pending' => $groupEntries->whereIn('status', ['pending', 'submitted', 'rejected'])->count(),
                        ]);

                    return (object) [
                        'project' => $project,
                        'groups' => $groups,
                        'pending' => $groups->sum('pending'),
                    ];
                })->values();

                return (object) [
                    'product' => $productProjects->first()->product,
                    'brands' => $brands,
                    'pending' => $brands->sum('pending'),
                ];
            })
            ->values();

        $renewalStatuses = $client->projects->mapWithKeys(
            fn(Project $project) => [$project->id => $this->renewalService->statusFor($project->renewal)]
        );

        return view('admin.clients.show', [
            'client' => $client,
            'activeTab' => $activeTab,
            'products' => $products,
            'projectsByProduct' => $projectsByProduct,
            'renewalStatuses' => $renewalStatuses,
            'salesEmployees' => SalesEmployee::where('status', 'active')->orderBy('name')->get(),
        ]);
    }
}
