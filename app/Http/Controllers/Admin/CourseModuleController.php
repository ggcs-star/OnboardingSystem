<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
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
}
