<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CourseQuizCheckpoint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseQuizAnswerController extends Controller
{
    public function store(Request $request, CourseQuizCheckpoint $checkpoint): JsonResponse
    {
        $client = $request->user()->client;
        abort_unless($client, 403);

        $data = $request->validate([
            'question_id' => ['required', 'exists:course_quiz_questions,id'],
            'selected_option_ids' => ['nullable', 'array'],
            'selected_option_ids.*' => ['integer'],
            'answer_text' => ['nullable', 'string'],
        ]);

        $question = $checkpoint->questions()->findOrFail($data['question_id']);

        $existing = $client->quizAnswers()->where('quiz_question_id', $question->id)->first();

        if ($existing) {
            return $this->respond($question, $existing);
        }

        if ($question->isAutoGraded()) {
            $selected = collect($data['selected_option_ids'] ?? [])->map(fn ($id) => (int) $id)->sort()->values();
            $correct = $question->options()->where('is_correct', true)->pluck('id')->sort()->values();

            $isCorrect = $selected->all() === $correct->all();

            $answer = $client->quizAnswers()->create([
                'quiz_checkpoint_id' => $checkpoint->id,
                'quiz_question_id' => $question->id,
                'selected_option_ids' => $selected->all(),
                'is_correct' => $isCorrect,
                'points_awarded' => $isCorrect ? $question->points : 0,
            ]);
        } else {
            $answer = $client->quizAnswers()->create([
                'quiz_checkpoint_id' => $checkpoint->id,
                'quiz_question_id' => $question->id,
                'answer_text' => $data['answer_text'] ?? '',
                'is_correct' => null,
                'points_awarded' => null,
            ]);
        }

        return $this->respond($question, $answer);
    }

    private function respond($question, $answer): JsonResponse
    {
        return response()->json([
            'pending' => $answer->isPending(),
            'is_correct' => $answer->is_correct,
            'points_awarded' => $answer->points_awarded,
            'correct_option_ids' => $question->type !== 'text'
                ? $question->options()->where('is_correct', true)->pluck('id')
                : [],
        ]);
    }
}
