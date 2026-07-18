@props(['groups', 'height' => 180])

<div class="flex items-end justify-around gap-4" style="height: {{ $height }}px">
    @foreach ($groups as $group)
        @php $total = collect($group['segments'])->sum('count'); @endphp
        <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
            <div class="flex w-full max-w-[48px] flex-col overflow-hidden rounded-t-lg bg-secondary-light" style="height: 100%">
                @foreach ($group['segments'] as $seg)
                    @if ($seg['count'] > 0)
                        @php $pct = $total > 0 ? round($seg['count'] / $total * 100) : 0; @endphp
                        <div
                            class="{{ $seg['classes'] }}"
                            style="height: {{ $pct }}%"
                            title="{{ $group['label'] }} — {{ $seg['label'] }}: {{ $seg['count'] }} ({{ $pct }}%)"
                            tabindex="0"
                        ></div>
                    @endif
                @endforeach
            </div>
            <div class="w-full text-center">
                <span class="block truncate text-xs font-medium text-secondary-dark">{{ $group['label'] }}</span>
                <span class="block text-[11px] text-secondary">{{ $total }} field{{ $total === 1 ? '' : 's' }}</span>
            </div>
        </div>
    @endforeach
</div>
