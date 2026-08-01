<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateDocumentEntryRequest;
use App\Http\Requests\Admin\UpdateDocumentValueRequest;
use App\Models\Project;
use App\Services\ProjectDocumentService;
use Illuminate\Http\RedirectResponse;

class ProjectDocumentController extends Controller
{
    public function __construct(private ProjectDocumentService $projectDocumentService)
    {
    }

    public function update(UpdateDocumentValueRequest $request, Project $project, string $group, string $field): RedirectResponse
    {
        $this->projectDocumentService->review(
            $project,
            $group,
            $field,
            $request->string('status')->value(),
            $request->input('remarks'),
            auth()->id(),
        );

        return back()->with('success', 'Document ' . $request->string('status') . '.');
    }

    public function edit(UpdateDocumentEntryRequest $request, Project $project, string $group, string $field): RedirectResponse
    {
        $this->projectDocumentService->adminEdit(
            $project,
            $group,
            $field,
            $request->input('value'),
            $request->file('file'),
            $request->string('status')->value(),
            $request->input('remarks'),
            auth()->id(),
        );

        return back()->with('success', 'Document updated.');
    }

    public function clear(Project $project, string $group, string $field): RedirectResponse
    {
        $this->projectDocumentService->clear($project, $group, $field);

        return back()->with('success', 'Submission cleared.');
    }
}
