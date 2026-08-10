<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseLessonController extends Controller
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    public function create(CourseModule $courseModule): View
    {
        $courseModule->load('course');

        return view('admin.courses.lessons.create', [
            'courseModule' => $courseModule,
        ]);
    }

    public function store(Request $request, CourseModule $courseModule): RedirectResponse
    {
        $data = $this->validateLesson($request);
        $data = $this->handleVideo($request, $data);

        $lesson = $courseModule->lessons()->create($data);

        return redirect()
            ->route('admin.course-lessons.edit', $lesson)
            ->with('success', 'Lesson created. Now add quiz checkpoints if needed.');
    }

    public function edit(CourseLesson $courseLesson): View
    {
        $courseLesson->load('module.course', 'checkpoints.questions.options');

        return view('admin.courses.lessons.edit', [
            'lesson' => $courseLesson,
            'courseModule' => $courseLesson->module,
        ]);
    }

    public function update(Request $request, CourseLesson $courseLesson): RedirectResponse
    {
        $data = $this->validateLesson($request);
        $data = $this->handleVideo($request, $data, $courseLesson);

        $courseLesson->update($data);

        return redirect()->route('admin.course-lessons.edit', $courseLesson)->with('success', 'Lesson updated.');
    }

    public function destroy(CourseLesson $courseLesson): RedirectResponse
    {
        $courseId = $courseLesson->module->course_id;
        $this->fileUploadService->delete($courseLesson->video_path);
        $courseLesson->delete();

        return redirect()
            ->route('admin.courses.show', ['course' => $courseId, 'tab' => 'modules'])
            ->with('success', 'Lesson removed.');
    }

    private function validateLesson(Request $request): array
    {
        if ($request->filled('video_url')) {
            $request->merge(['video_url' => $this->extractVideoUrl($request->input('video_url'))]);
        }

        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'video_source' => ['required', 'in:upload,youtube'],
            'video' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg', 'max:102400'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'duration' => ['nullable', 'string', 'max:50'],
        ]);
    }

    /**
     * Accepts either a plain YouTube/embed URL or a full pasted <iframe>
     * embed snippet (YouTube's "Copy embed code" option) and returns just
     * the URL, so both forms pass the `url` validation rule below.
     */
    private function extractVideoUrl(string $input): string
    {
        $input = trim($input);

        if (str_contains($input, '<iframe') && preg_match('/src=["\']([^"\']+)["\']/i', $input, $matches)) {
            $input = trim($matches[1]);
        }

        if (str_starts_with($input, '//')) {
            $input = 'https:' . $input;
        }

        return $input;
    }

    private function handleVideo(Request $request, array $data, ?CourseLesson $existing = null): array
    {
        if ($data['video_source'] === 'youtube') {
            if ($existing?->video_path) {
                $this->fileUploadService->delete($existing->video_path);
            }
            $data['video_path'] = null;
        } else {
            $data['video_url'] = null;

            if ($request->hasFile('video')) {
                if ($existing?->video_path) {
                    $this->fileUploadService->delete($existing->video_path);
                }
                $data['video_path'] = $this->fileUploadService->store($request->file('video'), 'course-lessons');
            }
        }

        unset($data['video']);

        return $data;
    }
}
