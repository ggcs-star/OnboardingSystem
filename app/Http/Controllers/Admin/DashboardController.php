<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientCourseLessonProgress;
use App\Models\ClientQuizAnswer;
use App\Models\Course;
use App\Models\CustomizationRequest;
use App\Models\LmsArticle;
use App\Models\LmsProduct;
use App\Models\Product;
use App\Models\ProductInquiry;
use App\Models\Project;
use App\Models\RenewalHistory;
use App\Models\SalesEmployee;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private ProjectService $projectService)
    {
    }

    public function index(Request $request): View
    {
        $projects = Project::with(['product', 'client'])->get();

        $stageCounts = collect(Project::STAGES)->map(function ($label, $key) use ($projects) {
            $stageProjects = $projects->where('current_stage', $key);

            $sample = $stageProjects->take(5)->map(
                fn (Project $project) => $project->product->name . ' - ' . ($project->brand_name ?? $project->project_name) . ' (' . $project->client->company_name . ')'
            )->implode(', ');

            $remaining = $stageProjects->count() - 5;
            if ($remaining > 0) {
                $sample .= " +{$remaining} more";
            }

            return ['label' => $label, 'value' => $stageProjects->count(), 'hint' => $sample];
        })->values();

        $productCounts = Product::withCount('projects')
            ->orderByDesc('projects_count')
            ->get()
            ->map(fn (Product $product) => ['label' => $product->name, 'value' => $product->projects_count])
            ->filter(fn ($row) => $row['value'] > 0)
            ->values();

        $documentStatusCounts = collect(Project::DOCUMENT_STATUSES)->map(
            fn ($status) => ['status' => $status, 'count' => $projects->flatMap->documentEntries()->where('status', $status)->count()]
        );

        $recentFilters = [
            'product' => $request->query('recent_product'),
            'stage' => $request->query('recent_stage'),
            'range' => $request->query('recent_range', 'all'),
        ];

        $recentRangeStart = match ($recentFilters['range']) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '365d' => now()->subDays(365),
            default => null,
        };

        $recentProjects = Project::with(['product', 'client'])
            ->when($recentFilters['product'], fn ($query) => $query->where('product_id', $recentFilters['product']))
            ->when($recentFilters['stage'], fn ($query) => $query->where('current_stage', $recentFilters['stage']))
            ->when($recentRangeStart, fn ($query) => $query->where('created_at', '>=', $recentRangeStart))
            ->latest()
            ->take(6)
            ->get();

        $allProducts = Product::orderBy('name')->get();

        $customizationCounts = CustomizationRequest::selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status');
        $pendingCustomizations = CustomizationRequest::with(['project.client', 'project.product'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $inquiryCounts = ProductInquiry::selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status');

        $inquiryFilters = [
            'status' => $request->query('inquiry_status'),
            'client' => $request->query('inquiry_client'),
            'range' => $request->query('inquiry_range', 'all'),
        ];

        $inquiryRangeStart = match ($inquiryFilters['range']) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '365d' => now()->subDays(365),
            default => null,
        };

        $filteredInquiries = ProductInquiry::with(['client', 'product'])
            ->when($inquiryFilters['status'], fn ($query) => $query->where('status', $inquiryFilters['status']))
            ->when($inquiryFilters['client'], fn ($query) => $query->where('client_id', $inquiryFilters['client']))
            ->when($inquiryRangeStart, fn ($query) => $query->where('created_at', '>=', $inquiryRangeStart))
            ->get();

        $trendStart = ($inquiryRangeStart ?? $filteredInquiries->min('created_at') ?? now()->subDays(90))->copy()->startOfDay();
        $trendSeconds = max($trendStart->diffInSeconds(now()), 7);

        $inquiryGroups = $filteredInquiries->groupBy('product_id');
        $inquiryProductsTotal = $inquiryGroups->count();

        $inquiryTable = $inquiryGroups
            ->map(function ($group) use ($trendStart, $trendSeconds) {
                $latest = $group->sortByDesc('created_at')->first();

                $trend = collect(range(0, 6))->map(function ($i) use ($group, $trendStart, $trendSeconds) {
                    $bucketStart = $trendStart->copy()->addSeconds((int) ($i * $trendSeconds / 7));
                    $bucketEnd = $trendStart->copy()->addSeconds((int) (($i + 1) * $trendSeconds / 7));

                    return $group->filter(fn (ProductInquiry $inquiry) => $inquiry->created_at->between($bucketStart, $bucketEnd))->count();
                })->values();

                return [
                    'product' => $latest->product,
                    'email' => $latest->email,
                    'client' => $latest->client,
                    'total' => $group->count(),
                    'status' => $latest->status,
                    'trend' => $trend,
                ];
            })
            ->sortByDesc('total')
            ->take(8)
            ->values();

        $inquiryClients = Client::whereHas('inquiries')->orderBy('company_name')->get();

        $topSalesEmployees = SalesEmployee::withCount('projects')
            ->where('status', 'active')
            ->orderByDesc('projects_count')
            ->take(5)
            ->get()
            ->filter(fn (SalesEmployee $employee) => $employee->projects_count > 0)
            ->map(fn (SalesEmployee $employee) => ['label' => $employee->name, 'value' => $employee->projects_count])
            ->values();

        $pendingQuizGrading = ClientQuizAnswer::whereNull('points_awarded')
            ->whereHas('question', fn ($query) => $query->where('type', 'text'))
            ->count();

        return view('admin.dashboard', [
            'productStats' => [
                'total' => Product::count(),
                'active' => Product::where('active', true)->count(),
            ],
            'clientStats' => [
                'total' => Client::count(),
                'active' => Client::where('status', 'active')->count(),
                'blocked' => Client::where('status', 'blocked')->count(),
            ],
            'projectStats' => $this->projectService->getDashboardStats(),
            'stageCounts' => $stageCounts,
            'productCounts' => $productCounts,
            'documentStatusCounts' => $documentStatusCounts,
            'recentProjects' => $recentProjects,
            'recentFilters' => $recentFilters,
            'allProducts' => $allProducts,
            'renewalStats' => [
                'revenue_this_month' => RenewalHistory::whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->sum('amount'),
                'revenue_total' => RenewalHistory::sum('amount'),
            ],
            'customizationCounts' => $customizationCounts,
            'pendingCustomizations' => $pendingCustomizations,
            'inquiryCounts' => $inquiryCounts,
            'inquiryTable' => $inquiryTable,
            'inquiryProductsTotal' => $inquiryProductsTotal,
            'inquiryClients' => $inquiryClients,
            'inquiryFilters' => $inquiryFilters,
            'topSalesEmployees' => $topSalesEmployees,
            'lmsStats' => [
                'products' => LmsProduct::count(),
                'articles' => LmsArticle::count(),
                'published_articles' => LmsArticle::where('is_published', true)->count(),
            ],
            'courseStats' => [
                'total' => Course::count(),
                'published' => Course::where('is_published', true)->count(),
                'lessons_started' => ClientCourseLessonProgress::count(),
                'lessons_completed' => ClientCourseLessonProgress::where('completed', true)->count(),
            ],
            'pendingQuizGrading' => $pendingQuizGrading,
        ]);
    }
}
