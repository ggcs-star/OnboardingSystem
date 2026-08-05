<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseQuizCheckpoint;
use App\Models\CourseQuizQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CourseQuizQuestionController extends Controller
{
    public function store(Request $request, CourseQuizCheckpoint $checkpoint): RedirectResponse
    {
        $data = $this->validateQuestion($request);

        $question = $checkpoint->questions()->create([
            'type' => $data['type'],
            'question_text' => $data['question_text'],
            'points' => $data['points'],
        ]);

        $this->syncOptions($question, $data);

        return $this->redirectTarget($checkpoint)->with('success', 'Question added.');
    }

    public function update(Request $request, CourseQuizQuestion $question): RedirectResponse
    {
        $data = $this->validateQuestion($request);

        $question->update([
            'type' => $data['type'],
            'question_text' => $data['question_text'],
            'points' => $data['points'],
        ]);

        $question->options()->delete();
        $this->syncOptions($question, $data);

        return $this->redirectTarget($question->checkpoint)->with('success', 'Question updated.');
    }

    public function destroy(CourseQuizQuestion $question): RedirectResponse
    {
        $checkpoint = $question->checkpoint;
        $question->delete();

        return $this->redirectTarget($checkpoint)->with('success', 'Question removed.');
    }

    private function redirectTarget(CourseQuizCheckpoint $checkpoint): RedirectResponse
    {
        return $checkpoint->isModuleQuiz()
            ? redirect()->route('admin.course-module-quizzes.edit', $checkpoint)
            : redirect()->route('admin.course-lessons.edit', $checkpoint->course_lesson_id);
    }

    private function validateQuestion(Request $request): array
    {
        $data = $request->validate([
            'type' => ['required', 'in:radio,checkbox,text'],
            'question_text' => ['required', 'string'],
            'points' => ['required', 'integer', 'min:1', 'max:1000'],
            'options' => ['required_unless:type,text', 'array'],
            'options.*' => ['nullable', 'string', 'max:255'],
            'correct' => ['nullable', 'array'],
            'correct.*' => ['integer'],
        ]);

        if ($data['type'] !== 'text') {
            $filledOptions = array_values(array_filter($data['options'] ?? [], fn ($option) => trim((string) $option) !== ''));

            if (count($filledOptions) < 2) {
                throw ValidationException::withMessages(['options' => 'Provide at least two options.']);
            }

            if (empty($data['correct'] ?? [])) {
                throw ValidationException::withMessages(['correct' => 'Mark at least one option as correct.']);
            }
        }

        return $data;
    }

    private function syncOptions(CourseQuizQuestion $question, array $data): void
    {
        if ($question->type === 'text') {
            return;
        }

        $correct = array_map('intval', $data['correct'] ?? []);

        foreach ($data['options'] as $index => $optionText) {
            if (trim((string) $optionText) === '') {
                continue;
            }

            $question->options()->create([
                'option_text' => $optionText,
                'is_correct' => in_array($index, $correct, true),
            ]);
        }
    }
}
