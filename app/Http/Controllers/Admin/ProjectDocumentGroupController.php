<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ProjectDocumentGroupController extends Controller
{
    public function index(Request $request, Project $project): View
    {
        $project->load(['client', 'product', 'salesEmployee']);

        $fieldMeta = $project->product->documentFields->keyBy('field_key');

        $entries = $project->documentEntries()
            ->map(function ($entry) use ($fieldMeta, $project) {
                $entry->placeholder = optional($fieldMeta->get($entry->field_key))->placeholder;
                $entry->project = $project;

                return $entry;
            })
            ->values();

        $groups = $entries->pluck('group_label', 'group_slug')->unique()->sort();

        $total = $entries->count();
        $submittedCount = $entries->whereIn('status', ['submitted', 'approved'])->count();
        $pendingCount = $entries->where('status', 'pending')->count();
        $rejectedCount = $entries->where('status', 'rejected')->count();

        $lastUpdated = $entries
            ->map(fn ($entry) => $entry->approved_at ?? $entry->submitted_at)
            ->filter()
            ->map(fn ($date) => Carbon::parse($date))
            ->max();

        $filtered = $entries
            ->when($request->filled('search'), function ($collection) use ($request) {
                $search = mb_strtolower($request->string('search'));

                return $collection->filter(fn ($entry) => str_contains(mb_strtolower($entry->label), $search));
            })
            ->when($request->filled('status'), fn ($collection) => $collection->filter(
                fn ($entry) => $entry->status === $request->string('status')->value()
            ))
            ->when($request->filled('type'), fn ($collection) => $collection->filter(
                fn ($entry) => $entry->type === $request->string('type')->value()
            ))
            ->when($request->filled('group'), fn ($collection) => $collection->filter(
                fn ($entry) => $entry->group_slug === $request->string('group')->value()
            ))
            ->values();

        $perPage = 10;
        $page = $request->integer('page', 1);

        $entriesPage = new LengthAwarePaginator(
            $filtered->forPage($page, $perPage)->values(),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.projects.documents.index', [
            'project' => $project,
            'groups' => $groups,
            'entries' => $entriesPage,
            'total' => $total,
            'submittedCount' => $submittedCount,
            'pendingCount' => $pendingCount,
            'rejectedCount' => $rejectedCount,
            'lastUpdated' => $lastUpdated,
            'readOnly' => $request->boolean('readonly'),
        ]);
    }

    public function show(Request $request, Project $project, string $group): View
    {
        $project->load(['client', 'product', 'salesEmployee']);

        abort_unless(isset($project->documents[$group]), 404);

        $fieldMeta = $project->product->documentFields->keyBy('field_key');

        $entries = $project->documentEntries()
            ->where('group_slug', $group)
            ->map(function ($entry) use ($fieldMeta, $project) {
                $entry->placeholder = optional($fieldMeta->get($entry->field_key))->placeholder;
                $entry->project = $project;

                return $entry;
            })
            ->values();

        $groupLabel = $project->documents[$group]['label'] ?? $group;
        $groupMandatory = (bool) ($project->documents[$group]['mandatory'] ?? false);

        $total = $entries->count();
        $submittedCount = $entries->whereIn('status', ['submitted', 'approved'])->count();
        $pendingCount = $entries->where('status', 'pending')->count();
        $rejectedCount = $entries->where('status', 'rejected')->count();

        $lastUpdated = $entries
            ->map(fn ($entry) => $entry->approved_at ?? $entry->submitted_at)
            ->filter()
            ->map(fn ($date) => Carbon::parse($date))
            ->max();

        $filtered = $entries
            ->when($request->filled('search'), function ($collection) use ($request) {
                $search = mb_strtolower($request->string('search'));

                return $collection->filter(fn ($entry) => str_contains(mb_strtolower($entry->label), $search));
            })
            ->when($request->filled('status'), fn ($collection) => $collection->filter(
                fn ($entry) => $entry->status === $request->string('status')->value()
            ))
            ->when($request->filled('type'), fn ($collection) => $collection->filter(
                fn ($entry) => $entry->type === $request->string('type')->value()
            ))
            ->values();

        $perPage = 10;
        $page = $request->integer('page', 1);

        $entriesPage = new LengthAwarePaginator(
            $filtered->forPage($page, $perPage)->values(),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.projects.documents.show', [
            'project' => $project,
            'groupSlug' => $group,
            'groupLabel' => $groupLabel,
            'groupMandatory' => $groupMandatory,
            'entries' => $entriesPage,
            'total' => $total,
            'submittedCount' => $submittedCount,
            'pendingCount' => $pendingCount,
            'rejectedCount' => $rejectedCount,
            'lastUpdated' => $lastUpdated,
            'readOnly' => $request->boolean('readonly'),
        ]);
    }
}
