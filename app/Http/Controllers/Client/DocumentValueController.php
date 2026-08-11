<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\BulkUpdateDocumentValuesRequest;
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

    public function bulkUpdate(BulkUpdateDocumentValuesRequest $request, Project $project): RedirectResponse
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);

        $onlyGroup = $request->input('group');

        foreach ($project->documentEntries() as $entry) {
            if ($onlyGroup !== null && $entry->group_slug !== $onlyGroup) {
                continue;
            }

            if ($entry->status === 'approved') {
                continue;
            }

            $file = $request->file("files.{$entry->group_slug}.{$entry->field_key}");
            $value = $request->input("fields.{$entry->group_slug}.{$entry->field_key}");

            if (! $file && ! filled($value)) {
                continue;
            }

            $this->projectDocumentService->submit($project, $entry->group_slug, $entry->field_key, $value, $file);
        }

        $this->renewalService->activateIfReady($project);

        [$mandatoryDone, $mandatoryTotal] = $project->mandatoryDocumentsProgress();

        if ($request->boolean('onboarding') && $mandatoryDone === $mandatoryTotal) {
            return redirect()->route('client.onboarding.subscription', $project);
        }

        return back()->with('success', 'Documents submitted for review.');
    }
}
