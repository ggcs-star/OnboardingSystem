<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseLesson;
use App\Models\CourseQuizCheckpoint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CourseQuizCheckpointController extends Controller
{
    public function store(Request $request, CourseLesson $courseLesson): RedirectResponse
    {
        $data = $this->validateCheckpoint($request, $courseLesson);
        $data['course_id'] = $courseLesson->module->course_id;

        $courseLesson->checkpoints()->create($data);

        return redirect()->route('admin.course-lessons.edit', $courseLesson)->with('success', 'Checkpoint added.');
    }

    public function update(Request $request, CourseQuizCheckpoint $checkpoint): RedirectResponse
    {
        $data = $this->validateCheckpoint($request, $checkpoint->lesson, $checkpoint);

        $checkpoint->update($data);

        return redirect()->route('admin.course-lessons.edit', $checkpoint->course_lesson_id)->with('success', 'Checkpoint updated.');
    }

    public function destroy(CourseQuizCheckpoint $checkpoint): RedirectResponse
    {
        $lessonId = $checkpoint->course_lesson_id;
        $checkpoint->delete();

        return redirect()->route('admin.course-lessons.edit', $lessonId)->with('success', 'Checkpoint removed.');
    }

    private function validateCheckpoint(Request $request, CourseLesson $lesson, ?CourseQuizCheckpoint $existing = null): array
    {
        $validated = $request->validate([
            'minutes' => ['required', 'integer', 'min:0'],
            'seconds' => ['required', 'integer', 'min:0', 'max:59'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $timestampSeconds = ($validated['minutes'] * 60) + $validated['seconds'];

        $duplicate = $lesson->checkpoints()
            ->where('timestamp_seconds', $timestampSeconds)
            ->when($existing, fn ($query) => $query->whereKeyNot($existing->id))
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages(['minutes' => 'A checkpoint already exists at that timestamp.']);
        }

        return [
            'timestamp_seconds' => $timestampSeconds,
            'title' => $validated['title'] ?? null,
        ];
    }
}
