<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSalesEmployeeRequest;
use App\Http\Requests\Admin\UpdateSalesEmployeeRequest;
use App\Models\SalesEmployee;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SalesEmployeeController extends Controller
{
    public function index(): View
    {
        $salesEmployees = SalesEmployee::withCount('projects')
            ->orderBy('name')
            ->get();

        return view('admin.sales-employees.index', ['salesEmployees' => $salesEmployees]);
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

    public function destroy(SalesEmployee $salesEmployee): RedirectResponse
    {
        $salesEmployee->projects()->update(['sales_employee_id' => null]);
        $salesEmployee->delete();

        return redirect()->route('admin.sales-employees.index')->with('success', 'Sales employee removed.');
    }
}
