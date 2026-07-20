<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_training_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('training_id')->constrained('product_training')->cascadeOnDelete();
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['client_id', 'training_id']);
        });

        $this->backfillFromProjectProgress();
    }

    public function down(): void
    {
        Schema::dropIfExists('client_training_progress');
    }

    /**
     * Training progress used to be tracked per-project. Collapse those rows
     * into one row per client+video, marking a video watched if it was
     * watched on any of the client's projects for that product.
     */
    private function backfillFromProjectProgress(): void
    {
        if (! Schema::hasTable('project_training_progress') || ! Schema::hasTable('projects')) {
            return;
        }

        $rows = DB::table('project_training_progress')
            ->join('projects', 'projects.id', '=', 'project_training_progress.project_id')
            ->select(
                'projects.client_id',
                'project_training_progress.training_id',
                'project_training_progress.completed',
                'project_training_progress.completed_at'
            )
            ->get()
            ->groupBy(fn ($row) => $row->client_id . ':' . $row->training_id);

        $now = now();

        foreach ($rows as $group) {
            $completed = $group->contains(fn ($row) => (bool) $row->completed);
            $completedAt = $completed
                ? $group->where('completed', true)->pluck('completed_at')->filter()->min()
                : null;

            DB::table('client_training_progress')->updateOrInsert(
                [
                    'client_id' => $group->first()->client_id,
                    'training_id' => $group->first()->training_id,
                ],
                [
                    'completed' => $completed,
                    'completed_at' => $completedAt,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
};
