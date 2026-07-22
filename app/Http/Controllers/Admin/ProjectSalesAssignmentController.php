<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectSalesAssignmentController extends Controller
{
    public function update(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'sales_employee_id' => ['nullable', 'exists:sales_employees,id'],
        ]);

        $project->update(['sales_employee_id' => $data['sales_employee_id'] ?? null]);

        return redirect()
            ->route('admin.clients.show', ['client' => $project->client_id, 'tab' => 'overview'])
            ->with('success', 'Salesperson updated for ' . ($project->brand_name ?? $project->project_name) . '.');
    }
}
