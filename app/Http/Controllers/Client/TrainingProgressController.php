<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTrainingProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TrainingProgressController extends Controller
{
    public function update(Request $request, Project $project, ProjectTrainingProgress $trainingProgress): RedirectResponse
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);
        abort_unless($trainingProgress->project_id === $project->id, 404);

        $trainingProgress->update([
            'completed' => ! $trainingProgress->completed,
            'completed_at' => ! $trainingProgress->completed ? now() : null,
        ]);

        return redirect()->route('client.projects.show', ['project' => $project, 'tab' => 'training']);
    }
}
