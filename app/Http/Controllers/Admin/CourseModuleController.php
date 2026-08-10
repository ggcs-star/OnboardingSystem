<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use App\Models\CourseQuizCheckpoint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseModuleController extends Controller
{
    public function store(Request $request, Course $course): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $course->modules()->create($data);

        return redirect()
            ->route('admin.courses.show', ['course' => $course, 'tab' => 'modules'])
            ->with('success', 'Module added.');
    }

    public function update(Request $request, CourseModule $courseModule): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $courseModule->update($data);

        return redirect()
            ->route('admin.courses.show', ['course' => $courseModule->course_id, 'tab' => 'modules'])
            ->with('success', 'Module updated.');
    }

    public function destroy(CourseModule $courseModule): RedirectResponse
    {
        $courseId = $courseModule->course_id;
        $courseModule->delete();

        return redirect()
            ->route('admin.courses.show', ['course' => $courseId, 'tab' => 'modules'])
            ->with('success', 'Module removed.');
    }

    public function reorder(Request $request, Course $course): RedirectResponse
    {
        $ids = $request->validate(['ids' => ['required', 'array']])['ids'];

        foreach ($ids as $index => $id) {
            CourseModule::where('id', $id)->where('course_id', $course->id)->update(['sort_order' => $index + 1]);
        }

        return redirect()->back();
    }

    /**
     * Reorders the module's interleaved lesson+quiz list in one shot. Each
     * id is prefixed ("lesson-12" / "quiz-5") using the same key format
     * CoursePlayerPayloadBuilder already builds for the client player, so
     * both lessons and quizzes can share one continuous sort_order sequence
     * within the module.
     */
    public function reorderItems(Request $request, CourseModule $courseModule): RedirectResponse
    {
        $ids = $request->validate(['ids' => ['required', 'array']])['ids'];

        foreach ($ids as $index => $id) {
            [$type, $itemId] = explode('-', $id, 2);

            match ($type) {
                'lesson' => CourseLesson::where('id', $itemId)->where('course_module_id', $courseModule->id)->update(['sort_order' => $index + 1]),
                'quiz' => CourseQuizCheckpoint::where('id', $itemId)->where('course_module_id', $courseModule->id)->update(['sort_order' => $index + 1]),
                default => null,
            };
        }

        return redirect()->back();
    }
}
