<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProjectDocumentValue;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentReviewController extends Controller
{
    public function index(Request $request): View
    {
        $baseQuery = ProjectDocumentValue::query();

        $values = ProjectDocumentValue::with(['project.client', 'project.product', 'documentField'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($q) use ($search) {
                    $q->whereHas('project', fn ($p) => $p->where('project_name', 'like', "%{$search}%")
                        ->orWhereHas('client', fn ($c) => $c->where('company_name', 'like', "%{$search}%")))
                        ->orWhereHas('documentField', fn ($f) => $f->where('label', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('product'), fn ($query) => $query->whereHas('project', fn ($p) => $p->where('product_id', $request->integer('product'))))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.documents.index', [
            'values' => $values,
            'products' => Product::orderBy('name')->get(),
            'stats' => [
                'total_fields' => (clone $baseQuery)->count(),
                'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
                'submitted' => (clone $baseQuery)->where('status', 'submitted')->count(),
                'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
                'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
            ],
        ]);
    }
}
