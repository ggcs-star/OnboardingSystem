<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\LmsProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LmsProductClientController extends Controller
{
    public function index(LmsProduct $lmsProduct): View
    {
        $assignedClients = $lmsProduct->clients()->orderBy('company_name')->get();
        $assignedClientIds = $assignedClients->pluck('id')->all();

        $availableClients = Client::with('user')
            ->whereNotIn('id', $assignedClientIds)
            ->orderBy('company_name')
            ->get();

        return view('admin.lms.products.clients', [
            'lmsProduct' => $lmsProduct,
            'assignedClients' => $assignedClients,
            'availableClients' => $availableClients,
        ]);
    }

    public function toggle(LmsProduct $lmsProduct, Client $client): RedirectResponse
    {
        $existing = $lmsProduct->clientProducts()->where('client_id', $client->id)->first();

        if ($existing) {
            $existing->delete();
            $message = 'Client unassigned from this LMS product.';
        } else {
            $lmsProduct->clientProducts()->create([
                'client_id' => $client->id,
                'status' => true,
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
            ]);
            $message = 'Client assigned to this LMS product.';
        }

        return redirect()
            ->route('admin.lms.products.clients.index', $lmsProduct)
            ->with('success', $message);
    }

    public function bulkAssign(Request $request, LmsProduct $lmsProduct): RedirectResponse
    {
        $validated = $request->validate([
            'client_ids' => ['required', 'array', 'min:1'],
            'client_ids.*' => ['integer', 'exists:clients,id'],
        ]);

        $alreadyAssignedIds = $lmsProduct->clientProducts()->pluck('client_id')->all();
        $newIds = array_diff($validated['client_ids'], $alreadyAssignedIds);

        foreach ($newIds as $clientId) {
            $lmsProduct->clientProducts()->create([
                'client_id' => $clientId,
                'status' => true,
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
            ]);
        }

        $count = count($newIds);

        return redirect()
            ->route('admin.lms.products.clients.index', $lmsProduct)
            ->with('success', $count > 0 ? "{$count} client(s) assigned." : 'No new clients were assigned.');
    }
}
