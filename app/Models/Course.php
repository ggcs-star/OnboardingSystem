<?php

namespace App\Models;

use App\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Course extends Model
{
    use HasFactory, HasSortOrder;

    protected $fillable = [
        'product_id',
        'title',
        'slug',
        'description',
        'thumbnail',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function sortOrderScopeColumn(): string
    {
        return 'product_id';
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->ordered();
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(CourseLesson::class, CourseModule::class);
    }

    public function thumbnailUrl(): ?string
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }

    /**
     * Every module's lessons and module quizzes interleaved (see
     * CourseModule::orderedItems()), each annotated with this client's
     * completion state. No locking — every item is always navigable; the
     * single-page player only uses `client_completed` for checkmarks and
     * to decide the "Next" target.
     */
    public function curriculumFor(Client $client): \Illuminate\Support\Collection
    {
        $this->loadMissing('modules.lessons', 'modules.quizzes.questions');

        $progressByLessonId = $client->courseLessonProgress()
            ->whereIn('course_lesson_id', $this->modules->flatMap->lessons->pluck('id'))
            ->get()
            ->keyBy('course_lesson_id');

        return $this->modules->map(function (CourseModule $module) use ($client, $progressByLessonId) {
            $items = $module->orderedItems()->map(function ($item) use ($client, $progressByLessonId) {
                if ($item->item_type === 'lesson') {
                    $progress = $progressByLessonId->get($item->id);
                    $item->setAttribute('client_completed', (bool) ($progress?->completed));
                    $item->setAttribute('client_last_position', $progress?->last_position_seconds ?? 0);
                } else {
                    $item->setAttribute('client_completed', $item->isCompletedFor($client));
                }

                return $item;
            });

            return tap($module)->setAttribute('items_for_client', $items);
        });
    }

    /** Flat, cross-module ordered item list — used for "what comes next" in the player. */
    public function flatCurriculumFor(Client $client): \Illuminate\Support\Collection
    {
        return $this->curriculumFor($client)->flatMap(fn (CourseModule $module) => $module->items_for_client);
    }

    /**
     * Same shape as curriculumFor(), but with no client — every item is
     * "fresh"/not completed. Used for the admin "preview as a client"
     * screen, where nothing should be marked watched or answered.
     */
    public function curriculumPreview(): \Illuminate\Support\Collection
    {
        $this->loadMissing('modules.lessons', 'modules.quizzes.questions');

        return $this->modules->map(function (CourseModule $module) {
            $items = $module->orderedItems()->map(function ($item) {
                $item->setAttribute('client_completed', false);
                $item->setAttribute('client_last_position', 0);

                return $item;
            });

            return tap($module)->setAttribute('items_for_client', $items);
        });
    }

    public function isCompletedFor(Client $client): bool
    {
        $modules = $this->curriculumFor($client);

        if ($modules->flatMap(fn (CourseModule $m) => $m->items_for_client)->where('item_type', 'lesson')->isEmpty()) {
            return false;
        }

        return $modules->every(fn (CourseModule $m) => $m->items_for_client->every(
            fn ($item) => $item->item_type === 'lesson'
                ? $item->client_completed
                : (! $item->is_required || $item->client_completed)
        ));
    }

    /**
     * Live-computed, points-weighted score across this course's quiz
     * questions (both mid-video checkpoints and module quizzes) for the
     * given client. `points_awarded` is the source of truth for earned
     * points — auto-graded (radio/checkbox) answers get full or zero at
     * submission time, manually-graded text answers get whatever an admin
     * awards (which may be partial credit, e.g. 4/5). Pending (ungraded)
     * text answers — and their points — are excluded from the denominator
     * so the percentage never reflects unknowns.
     */
    public function scoreFor(Client $client): object
    {
        $questions = CourseQuizQuestion::whereHas(
            'checkpoint',
            fn ($query) => $query->where('course_id', $this->id)
        )->get(['id', 'points']);

        $pointsByQuestionId = $questions->pluck('points', 'id');

        $answers = ClientQuizAnswer::where('client_id', $client->id)
            ->whereIn('quiz_question_id', $questions->pluck('id'))
            ->get();

        $graded = $answers->whereNotNull('points_awarded');
        $pending = $answers->whereNull('points_awarded')->count();

        $gradedCount = $graded->count();
        $correctCount = $graded->where('is_correct', true)->count();

        $gradedPoints = $graded->sum(fn ($answer) => $pointsByQuestionId->get($answer->quiz_question_id, 1));
        $earnedPoints = $graded->sum('points_awarded');

        return (object) [
            'graded_count' => $gradedCount,
            'correct_count' => $correctCount,
            'pending_count' => $pending,
            'graded_points' => $gradedPoints,
            'earned_points' => $earnedPoints,
            'percent' => $gradedPoints > 0 ? (int) round($earnedPoints / $gradedPoints * 100) : null,
        ];
    }
}
