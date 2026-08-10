<?php

namespace App\Services;

use App\Models\CourseModule;

/**
 * Slots a freshly-created module quiz into sort_order at the spot implied by
 * its after_course_lesson_id anchor. Called once at creation time only —
 * after that, dragging (via the reorder endpoint) is the sole thing that
 * moves sort_order, and the anchor is no longer read for display.
 */
class CourseModuleItemOrderer
{
    public function resequence(CourseModule $module): void
    {
        $module->loadMissing('lessons', 'quizzes');

        $quizzesByAnchor = [];
        foreach ($module->quizzes->sortBy('id') as $quiz) {
            $key = $quiz->after_course_lesson_id === null ? 'start' : $quiz->after_course_lesson_id;
            $quizzesByAnchor[$key][] = $quiz;
        }

        $order = 1;

        foreach ($quizzesByAnchor['start'] ?? [] as $quiz) {
            $quiz->update(['sort_order' => $order++]);
        }

        foreach ($module->lessons as $lesson) {
            $lesson->update(['sort_order' => $order++]);

            foreach ($quizzesByAnchor[$lesson->id] ?? [] as $quiz) {
                $quiz->update(['sort_order' => $order++]);
            }
        }
    }
}
