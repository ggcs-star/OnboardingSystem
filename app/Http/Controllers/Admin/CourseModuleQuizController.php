<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseModule;
use App\Models\CourseQuizCheckpoint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseModuleQuizController extends Controller
{
    public function store(Request $request, CourseModule $courseModule): RedirectResponse
    {
        $data = $this->validateQuiz($request, $courseModule);

        $quiz = $courseModule->quizzes()->create([
            'course_id' => $courseModule->course_id,
            'title' => $data['title'],
            'after_course_lesson_id' => $data['after_course_lesson_id'] ?: null,
            'is_required' => $data['is_required'],
        ]);

        return redirect()
            ->route('admin.course-module-quizzes.edit', $quiz)
            ->with('success', 'Quiz created. Now add questions below.');
    }

    public function edit(CourseQuizCheckpoint $checkpoint): View
    {
        abort_unless($checkpoint->isModuleQuiz(), 404);

        $checkpoint->load('module.course', 'questions.options');

        return view('admin.courses.module-quizzes.edit', [
            'quiz' => $checkpoint,
            'module' => $checkpoint->module,
        ]);
    }

    public function update(Request $request, CourseQuizCheckpoint $checkpoint): RedirectResponse
    {
        abort_unless($checkpoint->isModuleQuiz(), 404);

        $data = $this->validateQuiz($request, $checkpoint->module);

        $checkpoint->update([
            'title' => $data['title'],
            'after_course_lesson_id' => $data['after_course_lesson_id'] ?: null,
            'is_required' => $data['is_required'],
        ]);

        return redirect()->route('admin.course-module-quizzes.edit', $checkpoint)->with('success', 'Quiz updated.');
    }

    public function destroy(CourseQuizCheckpoint $checkpoint): RedirectResponse
    {
        abort_unless($checkpoint->isModuleQuiz(), 404);

        $courseId = $checkpoint->course_id;
        $checkpoint->delete();

        return redirect()
            ->route('admin.courses.show', ['course' => $courseId, 'tab' => 'modules'])
            ->with('success', 'Module quiz removed.');
    }

    private function validateQuiz(Request $request, CourseModule $courseModule): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'after_course_lesson_id' => [
                'nullable',
                Rule::exists('course_lessons', 'id')->where('course_module_id', $courseModule->id),
            ],
        ]);

        $data['is_required'] = $request->boolean('is_required');

        return $data;
    }
}
