<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseQuizCheckpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'course_lesson_id',
        'timestamp_seconds',
        'course_module_id',
        'after_course_lesson_id',
        'is_required',
        'title',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    public function afterLesson(): BelongsTo
    {
        return $this->belongsTo(CourseLesson::class, 'after_course_lesson_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(CourseQuizQuestion::class, 'quiz_checkpoint_id')->ordered();
    }

    public function isModuleQuiz(): bool
    {
        return ! is_null($this->course_module_id);
    }

    public function isCompletedFor(Client $client): bool
    {
        $questionIds = $this->questions->pluck('id');

        if ($questionIds->isEmpty()) {
            return false;
        }

        return $client->quizAnswers()->whereIn('quiz_question_id', $questionIds)->count() === $questionIds->count();
    }
}
