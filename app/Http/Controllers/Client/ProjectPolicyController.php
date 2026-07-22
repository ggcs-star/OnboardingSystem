<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectPolicyController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:20000'],
        ]);

        $project->policies()->create($data);

        return redirect()
            ->route('client.projects.show', ['project' => $project, 'tab' => 'policies'])
            ->with('success', 'Policy added.');
    }

    public function destroy(Request $request, Project $project, ProjectPolicy $policy): RedirectResponse
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);
        abort_unless($policy->project_id === $project->id, 404);

        $policy->delete();

        return redirect()
            ->route('client.projects.show', ['project' => $project, 'tab' => 'policies'])
            ->with('success', 'Policy removed.');
    }
}
