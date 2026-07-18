<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateDocumentValueRequest;
use App\Models\Project;
use App\Models\ProjectDocumentValue;
use Illuminate\Http\RedirectResponse;

class ProjectDocumentValueController extends Controller
{
    public function update(UpdateDocumentValueRequest $request, Project $project, ProjectDocumentValue $documentValue): RedirectResponse
    {
        abort_unless($documentValue->project_id === $project->id, 404);

        $documentValue->update([
            ...$request->validated(),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Document ' . $request->string('status') . '.');
    }
}
