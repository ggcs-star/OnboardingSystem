<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\UpdateOwnDocumentValueRequest;
use App\Models\Project;
use App\Services\ProjectDocumentService;
use App\Services\RenewalService;
use Illuminate\Http\RedirectResponse;

class DocumentValueController extends Controller
{
    public function __construct(
        private ProjectDocumentService $projectDocumentService,
        private RenewalService $renewalService,
    ) {
    }

    public function update(UpdateOwnDocumentValueRequest $request, Project $project, string $group, string $field): RedirectResponse
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);

        $this->projectDocumentService->submit(
            $project,
            $group,
            $field,
            $request->input('value'),
            $request->file('file'),
        );

        $this->renewalService->activateIfReady($project);

        return redirect()
            ->route('client.projects.show', ['project' => $project, 'tab' => 'documents'])
            ->with('success', 'Document submitted for review.');
    }
}
