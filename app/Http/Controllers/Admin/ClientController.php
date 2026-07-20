<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientRequest;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function __construct(private ClientService $clientService)
    {
    }

    public function index(Request $request): View
    {
        $clients = Client::with(['user', 'projects.documentValues', 'projects.tickets'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                        ->orWhere('owner_name', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.clients.index', ['clients' => $clients]);
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $this->clientService->createClient($request->validated());

        return redirect()->route('admin.clients.index')->with('success', 'Client added successfully.');
    }
}
