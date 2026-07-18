<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectDocumentValue extends Model
{
    use HasFactory;

    public const STATUSES = ['pending', 'submitted', 'approved', 'rejected'];

    protected $fillable = [
        'project_id',
        'document_field_id',
        'value',
        'file',
        'status',
        'remarks',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function documentField(): BelongsTo
    {
        return $this->belongsTo(ProductDocumentField::class, 'document_field_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
