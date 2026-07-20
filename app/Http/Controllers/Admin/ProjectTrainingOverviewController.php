<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\View\View;

class ProjectTrainingOverviewController extends Controller
{
    public function index(): View
    {
        $projects = Project::with(['product', 'client', 'trainingProgress'])
            ->latest()
            ->paginate(15);

        return view('admin.training.index', ['projects' => $projects]);
    }
}
