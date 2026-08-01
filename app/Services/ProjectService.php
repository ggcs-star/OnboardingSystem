<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectTimeline;
use App\Models\Renewal;
use App\Models\SupportTicket;
use Illuminate\Support\Carbon;

class ProjectService
{
    public function __construct(
        private RenewalService $renewalService,
        private ProjectDocumentService $projectDocumentService,
    ) {
    }

    public function createProject(array $data): Project
    {
        $project = Project::create([
            ...$data,
            'status' => 'active',
            'current_stage' => 'documents',
            'progress' => 0,
        ]);

        $project->load('product.documentFields', 'product.training', 'client');

        $this->projectDocumentService->initialize($project);

        foreach ($project->product->training as $video) {
            $project->client->trainingProgress()->firstOrCreate([
                'training_id' => $video->id,
            ], [
                'completed' => false,
            ]);
        }

        $project->timeline()->create([
            'stage' => 'documents',
            'completed' => false,
            'percentage' => 0,
        ]);

        return $project;
    }

    public function advanceStage(Project $project, string $stage): Project
    {
        $stages = array_keys(Project::STAGES);
        $targetIndex = array_search($stage, $stages, true);

        abort_if($targetIndex === false, 422, 'Invalid stage.');

        foreach ($stages as $index => $stageKey) {
            if ($index < $targetIndex) {
                ProjectTimeline::updateOrCreate(
                    ['project_id' => $project->id, 'stage' => $stageKey],
                    ['completed' => true, 'completed_at' => now(), 'percentage' => 100]
                );
            }
        }

        ProjectTimeline::updateOrCreate(
            ['project_id' => $project->id, 'stage' => $stage],
            ['completed' => false, 'percentage' => 0]
        );

        $project->update(['current_stage' => $stage]);

        if ($stage === 'application_live') {
            $project->update(['actual_live_date' => Carbon::today()]);
            $this->renewalService->createForProject($project);
        }

        return $project->fresh();
    }

    public function toggleBlocked(Project $project): Project
    {
        if ($project->status === 'inactive') {
            return $project;
        }

        $project->update(['status' => $project->status === 'blocked' ? 'active' : 'blocked']);

        return $project->fresh();
    }

    public function updateStatus(Project $project, string $status): Project
    {
        $project->update(['status' => $status]);

        return $project->fresh();
    }

    public function getDashboardStats(): array
    {
        $projects = Project::all();

        return [
            'active_projects' => $projects->where('status', 'active')->count(),
            'total_projects' => $projects->count(),
            'documents_pending' => $projects->filter(function (Project $project) {
                return $project->documentEntries()->contains(fn ($entry) => $entry->status !== 'approved');
            })->count(),
            'open_support_tickets' => SupportTicket::where('status', 'open')->count(),
            'renewals_alert' => Renewal::all()->filter(
                fn (Renewal $renewal) => in_array($this->renewalService->statusFor($renewal), ['expiring', 'expired'], true)
            )->count(),
        ];
    }
}
