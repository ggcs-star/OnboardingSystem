<x-client-layout title="Courses">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary-dark">Courses</h1>
            <p class="mt-1 text-sm text-secondary">Work through each product's courses — short videos with quizzes along the way</p>
        </div>

        @if ($assignedProducts->isNotEmpty())
            <form method="GET" class="w-full sm:w-72">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-secondary">Product</label>
                <select name="product_id" onchange="this.form.submit()"
                    class="w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                    @foreach ($assignedProducts as $product)
                        <option value="{{ $product->id }}" @selected($selectedProductId == $product->id)>{{ $product->name }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($courses as $course)
            @php $score = $course->scoreFor($client); @endphp
            <a href="{{ route('client.courses.show', $course) }}" class="overflow-hidden rounded-xl border border-app-border bg-white hover:border-primary/40 hover:shadow-sm">
                @if ($course->thumbnailUrl())
                    <img src="{{ $course->thumbnailUrl() }}" alt="" class="aspect-video w-full object-cover">
                @else
                    <div class="flex aspect-video w-full items-center justify-center bg-primary-light text-primary">
                        <x-icon name="video" class="w-8 h-8" />
                    </div>
                @endif
                <div class="p-5">
                    <h2 class="truncate font-semibold text-secondary-dark">{{ $course->title }}</h2>
                    <p class="text-xs text-secondary">{{ $course->lessons_count }} {{ Str::plural('lesson', $course->lessons_count) }}</p>
                    @if ($course->description)
                        <p class="mt-3 line-clamp-2 text-sm text-secondary">{{ $course->description }}</p>
                    @endif
                    @if (! is_null($score->percent))
                        <div class="mt-4">
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-surface-alt">
                                <div class="h-full rounded-full bg-success" style="width: {{ $score->percent }}%"></div>
                            </div>
                            <p class="mt-1.5 text-xs text-secondary">Score: {{ $score->percent }}%</p>
                        </div>
                    @endif
                </div>
            </a>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-app-border bg-white p-10 text-center text-sm text-secondary">
                @if ($assignedProducts->isEmpty())
                    You don't have any products assigned yet.
                @else
                    No courses published for this product yet.
                @endif
            </div>
        @endforelse
    </div>
</x-client-layout>
