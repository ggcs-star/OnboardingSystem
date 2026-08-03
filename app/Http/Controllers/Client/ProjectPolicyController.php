<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectPolicyController extends Controller
{
    public function index(Request $request, Project $project): View
    {
        abort_unless($project->client->user_id === $request->user()->id, 403);

        $project->load('product.policies');

        return view('client.projects.policies', ['project' => $project]);
    }
}
