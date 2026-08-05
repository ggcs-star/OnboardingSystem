<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientQuizAnswer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseQuizReviewController extends Controller
{
    public function index(): View
    {
        $pendingAnswers = ClientQuizAnswer::whereNull('points_awarded')
            ->whereHas('question', fn ($query) => $query->where('type', 'text'))
            ->with(['client', 'question.checkpoint.course', 'question.checkpoint.lesson', 'question.checkpoint.module'])
            ->latest()
            ->paginate(15);

        return view('admin.courses.quiz-review.index', [
            'pendingAnswers' => $pendingAnswers,
        ]);
    }

    /**
     * Text answers get partial credit, not just right/wrong — the admin
     * awards any whole number of points up to the question's max, and
     * "fully correct" (is_correct, used for the check/x badge elsewhere)
     * is derived as having earned every point.
     */
    public function grade(Request $request, ClientQuizAnswer $answer): RedirectResponse
    {
        $maxPoints = $answer->question->points;

        $data = $request->validate([
            'points_awarded' => ['required', 'integer', 'min:0', "max:{$maxPoints}"],
        ]);

        $answer->update([
            'points_awarded' => $data['points_awarded'],
            'is_correct' => $data['points_awarded'] >= $maxPoints,
            'graded_by' => $request->user()->id,
            'graded_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Answer graded.');
    }
}
