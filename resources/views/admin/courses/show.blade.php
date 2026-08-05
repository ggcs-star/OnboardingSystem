@php
    $tabs = [
        'details' => ['label' => 'Details', 'icon' => 'edit'],
        'modules' => ['label' => 'Modules & Lessons', 'icon' => 'file-text'],
    ];
@endphp

<x-admin-layout title="Edit Course">
    <nav class="flex items-center gap-2 text-sm text-secondary">
        <a href="{{ route('admin.courses.index') }}" class="hover:text-primary">Courses</a>
        <x-icon name="chevron-right" class="w-3.5 h-3.5" />
        <span class="font-medium text-secondary-dark">{{ $course->title }}</span>
    </nav>

    <div class="mt-3 flex items-start justify-between gap-4">
        <div class="flex items-start gap-4">
            @if ($course->thumbnailUrl())
                <img src="{{ $course->thumbnailUrl() }}" alt="" class="h-12 w-12 shrink-0 rounded-lg object-cover">
            @else
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary-light text-primary">
                    <x-icon name="video" class="w-6 h-6" />
                </span>
            @endif
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-semibold text-secondary-dark">{{ $course->title }}</h1>
                    <x-badge :classes="status_badge_classes($course->is_published)" dot>
                        {{ $course->is_published ? 'Published' : 'Draft' }}
                    </x-badge>
                </div>
                <p class="text-sm text-secondary">{{ $course->product?->name }} — manage details, modules and lessons.</p>
            </div>
        </div>
        <a href="{{ route('admin.courses.preview', $course) }}" target="_blank"
            class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-chart-4/30 bg-chart-4/15 px-4 py-2 text-sm font-medium text-chart-4 hover:border-chart-4/60 hover:bg-chart-4/25">
            <x-icon name="eye" class="w-4 h-4" />
            Preview as Client
        </a>
    </div>

    <div class="mt-6 border-b border-app-border">
        <nav class="-mb-px flex gap-6 overflow-x-auto">
            @foreach ($tabs as $key => $tab)
                <x-tab-link
                    :href="route('admin.courses.show', ['course' => $course, 'tab' => $key])"
                    :active="$activeTab === $key"
                    :icon="$tab['icon']"
                >
                    {{ $tab['label'] }}
                </x-tab-link>
            @endforeach
        </nav>
    </div>

    <div class="mt-6">
        @include('admin.courses.tabs.' . $activeTab)
    </div>
</x-admin-layout>
