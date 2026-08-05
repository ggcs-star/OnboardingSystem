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
     * Lessons and module quizzes interleaved in curriculum order — quizzes
     * are inserted immediately after the lesson they're anchored to
     * (after_course_lesson_id), or at the start of the module if null.
     * Each item is tagged with a virtual `item_type` of 'lesson' or
     * 'module_quiz'. Used by both the admin modules tab and the client
     * single-page player so the ordering logic lives in exactly one place.
     */
    public function orderedItems(): Collection
    {
        $quizzesByAnchor = $this->quizzes->groupBy('after_course_lesson_id');

        $items = collect();

        foreach ($quizzesByAnchor->get(null, collect())->sortBy('id') as $quiz) {
            $items->push(tap($quiz)->setAttribute('item_type', 'module_quiz'));
        }

        foreach ($this->lessons as $lesson) {
            $items->push(tap($lesson)->setAttribute('item_type', 'lesson'));

            foreach ($quizzesByAnchor->get($lesson->id, collect())->sortBy('id') as $quiz) {
                $items->push(tap($quiz)->setAttribute('item_type', 'module_quiz'));
            }
        }

        return $items;
    }
}
