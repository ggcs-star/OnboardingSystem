<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    use HasFactory;

    public const STAGES = [
        'documents' => 'Document Template',
        'development' => 'Development',
        'testing' => 'Testing',
        'training' => 'Client Training',
        'live' => 'Live',
    ];

    protected $fillable = [
        'product_id',
        'client_id',
        'project_name',
        'expected_live_date',
        'actual_live_date',
        'status',
        'current_stage',
        'progress',
        'assigned_manager',
    ];

    protected $casts = [
        'expected_live_date' => 'date',
        'actual_live_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_manager');
    }

    public function timeline(): HasMany
    {
        return $this->hasMany(ProjectTimeline::class)->orderBy('id');
    }

    public function documentValues(): HasMany
    {
        return $this->hasMany(ProjectDocumentValue::class);
    }

    public function trainingProgress(): HasMany
    {
        return $this->hasMany(ProjectTrainingProgress::class);
    }

    public function renewal(): HasOne
    {
        return $this->hasOne(Renewal::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    /**
     * [count submitted-or-approved, total] document fields for this project.
     */
    public function documentsProgress(): array
    {
        $total = $this->documentValues->count();
        $done = $this->documentValues->whereIn('status', ['submitted', 'approved'])->count();

        return [$done, $total];
    }

    /**
     * [count completed, total] training videos for this project.
     */
    public function trainingProgressCount(): array
    {
        $total = $this->trainingProgress->count();
        $done = $this->trainingProgress->where('completed', true)->count();

        return [$done, $total];
    }
}
