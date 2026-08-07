<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSalesEmployeeRequest;
use App\Http\Requests\Admin\UpdateSalesEmployeeRequest;
use App\Models\Product;
use App\Models\SalesEmployee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SalesEmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $salesEmployees = SalesEmployee::with(['projects.product'])
            ->when($request->filled('product'), function ($query) use ($request) {
                $query->whereHas('projects', fn ($p) => $p->where('product_id', $request->product));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();

        $products = Product::orderBy('name')->get();

        return view('admin.sales-employees.index', ['salesEmployees' => $salesEmployees, 'products' => $products]);
    }

    public function store(StoreSalesEmployeeRequest $request): RedirectResponse
    {
        SalesEmployee::create($request->validated());

        return redirect()->route('admin.sales-employees.index')->with('success', 'Sales employee added successfully.');
    }

    public function show(SalesEmployee $salesEmployee): View
    {
        $salesEmployee->load(['projects.client', 'projects.product']);

        return view('admin.sales-employees.show', ['salesEmployee' => $salesEmployee]);
    }

    public function update(UpdateSalesEmployeeRequest $request, SalesEmployee $salesEmployee): RedirectResponse
    {
        $salesEmployee->update($request->validated());

        return redirect()->route('admin.sales-employees.show', $salesEmployee)->with('success', 'Sales employee updated.');
    }

    public function updateStatus(Request $request, SalesEmployee $salesEmployee): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $salesEmployee->update($data);

        return back()->with('success', 'Sales employee status updated.');
    }

    public function destroy(SalesEmployee $salesEmployee): RedirectResponse
    {
        $salesEmployee->projects()->update(['sales_employee_id' => null]);
        $salesEmployee->delete();

        return redirect()->route('admin.sales-employees.index')->with('success', 'Sales employee removed.');
    }
}
