<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientQuizAnswer;
use App\Models\Course;
use App\Models\Product;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    public function index(Request $request): View
    {
        $courses = Course::with('product')
            ->withCount(['modules', 'lessons'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where('title', 'like', "%{$search}%");
            })
            ->when($request->filled('product_id'), fn ($query) => $query->where('product_id', $request->input('product_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('is_published', $request->input('status') === 'published'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.courses.index', [
            'courses' => $courses,
            'products' => Product::orderBy('name')->get(),
            'pendingReviewCount' => ClientQuizAnswer::whereNull('is_correct')->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
        ]);

        $slug = $this->uniqueSlug($data['title']);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->fileUploadService->store($request->file('thumbnail'), 'course-thumbnails');
        }

        $data['slug'] = $slug;

        Course::create($data);

        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully.');
    }

    public function show(Request $request, Course $course): View
    {
        $tabs = ['details', 'modules'];

        $activeTab = $request->query('tab', 'details');
        if (! in_array($activeTab, $tabs, true)) {
            $activeTab = 'details';
        }

        $course->load('modules.lessons.checkpoints.questions.options', 'modules.quizzes.questions.options', 'product');

        return view('admin.courses.show', [
            'course' => $course,
            'activeTab' => $activeTab,
        ]);
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('thumbnail')) {
            $this->fileUploadService->delete($course->thumbnail);
            $data['thumbnail'] = $this->fileUploadService->store($request->file('thumbnail'), 'course-thumbnails');
        }

        $course->update($data);

        return redirect()->route('admin.courses.show', $course)->with('success', 'Course updated.');
    }

    public function togglePublish(Course $course): RedirectResponse
    {
        $course->update(['is_published' => ! $course->is_published]);

        return redirect()->back()->with('success', $course->is_published ? 'Course published.' : 'Course unpublished.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $this->fileUploadService->delete($course->thumbnail);
        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Course deleted.');
    }

    private function uniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $suffix = 1;

        while (Course::where('slug', $slug)->exists()) {
            $suffix++;
            $slug = $baseSlug . '-' . $suffix;
        }

        return $slug;
    }
}
