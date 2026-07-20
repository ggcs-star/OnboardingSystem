<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTrainingProgress extends Model
{
    use HasFactory;

    protected $table = 'project_training_progress';

    protected $fillable = [
        'project_id',
        'training_id',
        'completed',
        'completed_at',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(ProductTraining::class, 'training_id');
    }
}
