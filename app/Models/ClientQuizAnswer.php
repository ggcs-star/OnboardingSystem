<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientQuizAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'quiz_checkpoint_id',
        'quiz_question_id',
        'answer_text',
        'selected_option_ids',
        'is_correct',
        'points_awarded',
        'graded_by',
        'graded_at',
    ];

    protected $casts = [
        'selected_option_ids' => 'array',
        'is_correct' => 'boolean',
        'graded_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(CourseQuizCheckpoint::class, 'quiz_checkpoint_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(CourseQuizQuestion::class, 'quiz_question_id');
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function isPending(): bool
    {
        return is_null($this->points_awarded);
    }
}
