<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseQuizQuestion;
use App\Services\CoursePlayerPayloadBuilder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(private CoursePlayerPayloadBuilder $payloadBuilder)
    {
    }

    public function index(Request $request): View
    {
        $client = $request->user()->client;
        $assignedProducts = $this->assignedProducts($client);

        $selectedProductId = $request->integer('product_id') ?: $assignedProducts->first()?->id;

        $courses = $selectedProductId
            ? Course::where('product_id', $selectedProductId)
                ->where('is_published', true)
                ->withCount('lessons')
                ->ordered()
                ->get()
            : collect();

        return view('client.courses.index', [
            'assignedProducts' => $assignedProducts,
            'selectedProductId' => $selectedProductId,
            'courses' => $courses,
            'client' => $client,
        ]);
    }

    /**
     * Single-page player: the whole curriculum (every lesson's video config
     * and every module quiz's questions) is built up front and handed to
     * the client as one JSON payload, so switching between items is a pure
     * client-side swap (see resources/js/course-player.js) rather than a
     * page navigation. The sidebar itself is server-rendered from $modules.
     */
    public function show(Request $request, Course $course): View
    {
        $client = $request->user()->client;
        abort_unless($client, 403);
        abort_unless($course->is_published, 404);
        abort_unless($this->assignedProducts($client)->contains('id', $course->product_id), 403);

        $course->load('product');
        $modules = $course->curriculumFor($client);

        $questionIds = CourseQuizQuestion::whereHas(
            'checkpoint',
            fn ($query) => $query->where('course_id', $course->id)
        )->pluck('id');

        $answersByQuestionId = $client->quizAnswers()
            ->whereIn('quiz_question_id', $questionIds)
            ->get()
            ->keyBy('quiz_question_id');

        [$itemsPayload, $order] = $this->payloadBuilder->build(
            $modules,
            fn ($lesson) => route('client.course-lessons.progress.update', $lesson),
            fn ($checkpoint) => route('client.course-quiz-answers.store', $checkpoint),
            $answersByQuestionId
        );

        $requestedItem = $request->query('item');
        $initialItemKey = ($requestedItem && isset($itemsPayload[$requestedItem]))
            ? $requestedItem
            : (collect($order)->first(fn ($key) => ! $itemsPayload[$key]['completed']) ?? ($order[0] ?? null));

        return view('client.courses.show', [
            'course' => $course,
            'modules' => $modules,
            'score' => $course->scoreFor($client),
            'completed' => $course->isCompletedFor($client),
            'playerData' => [
                'courseId' => $course->id,
                'items' => $itemsPayload,
                'order' => $order,
                'initialItemKey' => $initialItemKey,
            ],
        ]);
    }

    private function assignedProducts($client)
    {
        if (! $client) {
            return collect();
        }

        return $client->products()
            ->wherePivot('status', true)
            ->where('active', true)
            ->orderBy('name')
            ->get();
    }
}
