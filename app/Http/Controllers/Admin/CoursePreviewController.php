<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseQuizCheckpoint;
use App\Services\CoursePlayerPayloadBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Lets an admin browse a course exactly as an assigned client would see it
 * — same single-page player, same quiz interactions — without needing a
 * real Client record. Nothing here is persisted: progress is a no-op and
 * quiz answers are graded on the fly and discarded, so previewing never
 * pollutes a real client's data or this course's own stats.
 */
class CoursePreviewController extends Controller
{
    public function __construct(private CoursePlayerPayloadBuilder $payloadBuilder)
    {
    }

    public function show(Request $request, Course $course): View
    {
        $course->load('product');
        $modules = $course->curriculumPreview();

        [$itemsPayload, $order] = $this->payloadBuilder->build(
            $modules,
            fn ($lesson) => route('admin.course-lessons.preview-progress', $lesson),
            fn ($checkpoint) => route('admin.course-quiz-answers.preview-store', $checkpoint),
            collect()
        );

        $requestedItem = $request->query('item');
        $initialItemKey = ($requestedItem && isset($itemsPayload[$requestedItem])) ? $requestedItem : ($order[0] ?? null);

        return view('admin.courses.preview', [
            'course' => $course,
            'modules' => $modules,
            'playerData' => [
                'courseId' => $course->id,
                'items' => $itemsPayload,
                'order' => $order,
                'initialItemKey' => $initialItemKey,
            ],
        ]);
    }

    public function progress(CourseLesson $courseLesson): JsonResponse
    {
        return response()->json(['ok' => true]);
    }

    public function quizAnswer(Request $request, CourseQuizCheckpoint $checkpoint): JsonResponse
    {
        $data = $request->validate([
            'question_id' => ['required', 'exists:course_quiz_questions,id'],
            'selected_option_ids' => ['nullable', 'array'],
            'selected_option_ids.*' => ['integer'],
            'answer_text' => ['nullable', 'string'],
        ]);

        $question = $checkpoint->questions()->findOrFail($data['question_id']);

        if (! $question->isAutoGraded()) {
            return response()->json(['pending' => true, 'is_correct' => null, 'points_awarded' => null, 'correct_option_ids' => []]);
        }

        $selected = collect($data['selected_option_ids'] ?? [])->map(fn ($id) => (int) $id)->sort()->values();
        $correct = $question->options()->where('is_correct', true)->pluck('id')->sort()->values();
        $isCorrect = $selected->all() === $correct->all();

        return response()->json([
            'pending' => false,
            'is_correct' => $isCorrect,
            'points_awarded' => $isCorrect ? $question->points : 0,
            'correct_option_ids' => $question->options()->where('is_correct', true)->pluck('id'),
        ]);
    }
}
