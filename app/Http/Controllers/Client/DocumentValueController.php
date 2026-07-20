<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\UpdateOwnDocumentValueRequest;
use App\Models\Project;
use App\Models\ProjectDocumentValue;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;

class DocumentValueController extends Controller
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    public function update(UpdateOwnDocumentValueRequest $request, Project $project, ProjectDocumentValue $documentValue): RedirectResponse
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);
        abort_unless($documentValue->project_id === $project->id, 404);

        $data = ['status' => 'submitted'];

        if ($request->hasFile('file')) {
            $this->fileUploadService->delete($documentValue->file);
            $data['file'] = $this->fileUploadService->store($request->file('file'), 'project-documents');
            $data['value'] = null;
        } else {
            $data['value'] = $request->input('value');
        }

        $documentValue->update($data);

        return redirect()
            ->route('client.projects.show', ['project' => $project, 'tab' => 'documents'])
            ->with('success', 'Document submitted for review.');
    }
}
