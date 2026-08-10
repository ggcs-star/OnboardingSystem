<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class CourseModule extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'sort_order',
    ];

    public function sortOrderScopeColumn(): string
    {
        return 'course_id';
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(CourseLesson::class)->ordered();
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(CourseQuizCheckpoint::class, 'course_module_id');
    }

    /**
     * Lessons and module quizzes interleaved in curriculum order, driven by
     * the sort_order both share within this module (see the drag-and-drop
     * reorder endpoint). Each item is tagged with a virtual `item_type` of
     * 'lesson' or 'module_quiz'. Used by both the admin modules tab and the
     * client single-page player so the ordering logic lives in exactly one
     * place.
     */
    public function orderedItems(): Collection
    {
        $lessons = $this->lessons->map(fn (CourseLesson $lesson) => tap($lesson)->setAttribute('item_type', 'lesson'));
        $quizzes = $this->quizzes->map(fn (CourseQuizCheckpoint $quiz) => tap($quiz)->setAttribute('item_type', 'module_quiz'));

        return $lessons->concat($quizzes)->sortBy('sort_order')->values();
    }
}
