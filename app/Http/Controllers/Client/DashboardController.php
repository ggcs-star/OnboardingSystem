<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CustomizationRequest;
use App\Models\Project;
use App\Models\Renewal;
use App\Services\RenewalService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private RenewalService $renewalService)
    {
    }

    public function index(Request $request): View
    {
        $client = $request->user()->client;

        $projects = $client
            ? $client->projects()->with(['product', 'tickets', 'renewal'])->latest()->get()
            : collect();

        $docsDone = 0;
        $docsTotal = 0;

        foreach ($projects as $project) {
            [$done, $total] = $project->documentsProgress();
            $docsDone += $done;
            $docsTotal += $total;
        }

        $documentStatusCounts = collect(Project::DOCUMENT_STATUSES)->map(
            fn ($status) => [
                'status' => $status,
                'count' => $projects->flatMap->documentEntries()->where('status', $status)->count(),
            ]
        );

        $renewals = $projects->map(fn (Project $project) => $project->renewal)
            ->filter()
            ->map(fn (Renewal $renewal) => tap($renewal)->setAttribute('renewal_status', $this->renewalService->statusFor($renewal)))
            ->sortBy('expiry_date')
            ->values();

        $assignedProductIds = $client
            ? $client->products()->wherePivot('status', true)->where('active', true)->pluck('products.id')
            : collect();

        $courses = $assignedProductIds->isNotEmpty()
            ? Course::with(['product', 'lessons'])
                ->whereIn('product_id', $assignedProductIds)
                ->where('is_published', true)
                ->withCount('lessons')
                ->get()
            : collect();

        $courseLessonsDone = 0;
        $courseLessonsTotal = 0;

        $courseProgress = $courses->map(function (Course $course) use ($client, &$courseLessonsDone, &$courseLessonsTotal) {
            $total = $course->lessons_count;
            $done = $total > 0
                ? $client->courseLessonProgress()
                    ->whereIn('course_lesson_id', $course->lessons->pluck('id'))
                    ->where('completed', true)
                    ->count()
                : 0;

            $courseLessonsDone += $done;
            $courseLessonsTotal += $total;

            return [
                'label' => $course->title,
                'value' => $total > 0 ? round($done / $total * 100) : 0,
                'hint' => $done . '/' . $total . ' lessons',
                'color' => match (true) {
                    $total > 0 && $done === $total => 'bg-success',
                    $done > 0 => 'bg-warning',
                    default => 'bg-secondary',
                },
            ];
        })->values();

        $projectIds = $projects->pluck('id');

        $customizationCounts = CustomizationRequest::whereIn('project_id', $projectIds)
            ->selectRaw('status, count(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $recentCustomizationRequests = CustomizationRequest::with('project')
            ->whereIn('project_id', $projectIds)
            ->latest()
            ->take(5)
            ->get();

        return view('client.dashboard', [
            'projects' => $projects,
            'documentStatusCounts' => $documentStatusCounts,
            'renewals' => $renewals,
            'courseProgress' => $courseProgress,
            'customizationCounts' => $customizationCounts,
            'recentCustomizationRequests' => $recentCustomizationRequests,
            'stats' => [
                'total_projects' => $projects->count(),
                'active_projects' => $projects->where('status', 'active')->count(),
                'docs_done' => $docsDone,
                'docs_total' => $docsTotal,
                'open_tickets' => $projects->flatMap->tickets->where('status', 'open')->count(),
                'course_lessons_done' => $courseLessonsDone,
                'course_lessons_total' => $courseLessonsTotal,
                'pending_customizations' => $customizationCounts->get('pending', 0),
            ],
        ]);
    }
}
