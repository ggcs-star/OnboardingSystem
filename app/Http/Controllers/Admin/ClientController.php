<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientRequest;
use App\Http\Requests\Admin\UpdateClientRequest;
use App\Models\Client;
use App\Services\ClientService;
use App\Services\RenewalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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
        $query = Client::with(['user', 'projects.product.documentGroups.fields', 'products.documentGroups.fields'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                        ->orWhere('owner_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('onboarded_from'), fn($query) => $query->whereDate('created_at', '>=', $request->string('onboarded_from')))
            ->when($request->filled('onboarded_to'), fn($query) => $query->whereDate('created_at', '<=', $request->string('onboarded_to')))
            ->latest();

        if ($request->filled('docs')) {
            $docsFilter = $request->string('docs')->toString();

            $filtered = $query->get()->filter(function (Client $client) use ($docsFilter) {
                $summary = $client->documentsSummary();

                return $docsFilter === 'complete'
                    ? $summary->total > 0 && $summary->pending === 0
                    : $summary->pending > 0;
            })->values();

            $page = $request->integer('page', 1);
            $perPage = 10;

            $clients = new LengthAwarePaginator(
                $filtered->forPage($page, $perPage)->values(),
                $filtered->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $clients = $query->paginate(10)->withQueryString();
        }

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

    public function show(Client $client): View
    {
        $client->load(['user', 'products']);

        return view('admin.clients.show', [
            'client' => $client,
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $this->clientService->updateClient($client, $request->validated());

        return back()->with('success', 'Client updated.');
    }

    public function toggleStatus(Client $client): RedirectResponse
    {
        $this->clientService->toggleStatus($client);

        return back()->with('success', 'Client status updated.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        if ($client->projects()->where('status', 'active')->exists()) {
            return back()->with('error', 'This client has an active project — deactivate the client instead of deleting.');
        }

        $this->clientService->deleteClient($client);

        return redirect()->route('admin.clients.index')->with('success', 'Client deleted.');
    }
}
