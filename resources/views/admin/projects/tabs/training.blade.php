<div class="space-y-3">
    @forelse ($project->product->training as $video)
        @php $progress = $project->trainingProgressFor($video); @endphp
        <div class="flex items-center justify-between rounded-xl border border-app-border bg-white p-4">
            <div>
                <h3 class="font-medium text-secondary-dark">{{ $video->title }}</h3>
                <p class="text-xs text-secondary">{{ $video->duration }}</p>
            </div>
            @if ($progress?->completed)
                <x-badge classes="bg-success-light text-success">
                    <x-icon name="check" class="w-3 h-3" />
                    Watched {{ $progress->completed_at?->format('d-M-Y') }}
                </x-badge>
            @else
                <x-badge classes="bg-secondary-light text-secondary-dark">Not watched yet</x-badge>
            @endif
        </div>
    @empty
        <div class="rounded-xl border border-dashed border-app-border bg-white p-8 text-center text-sm text-secondary">
            No training videos for this product.
        </div>
    @endforelse
</div>
