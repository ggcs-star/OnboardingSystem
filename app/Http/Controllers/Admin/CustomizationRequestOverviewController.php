<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomizationRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomizationRequestOverviewController extends Controller
{
    public function index(Request $request): View
    {
        $requests = CustomizationRequest::with(['project.client', 'project.product', 'createdBy', 'reviewedBy'])
            ->when($request->filled('product'), function ($query) use ($request) {
                $query->whereHas('project', fn ($p) => $p->where('product_id', $request->product));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhereHas('project.client', fn ($c) => $c->where('company_name', 'like', "%{$search}%"))
                        ->orWhereHas('project', fn ($p) => $p->where('project_name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $products = Product::orderBy('name')->get();

        return view('admin.customization-requests.index', ['requests' => $requests, 'products' => $products]);
    }
}
