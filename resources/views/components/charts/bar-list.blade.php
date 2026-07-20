@props(['data', 'unit' => ''])

@php
    $max = collect($data)->max('value') ?: 1;
@endphp

<div class="space-y-3" role="table" aria-label="Bar chart">
    @foreach ($data as $row)
        @php
            $pct = $max > 0 ? round(($row['value'] / $max) * 100) : 0;
            $barColor = $row['color'] ?? 'bg-chart-1';
        @endphp
        <div class="group" role="row">
            <div class="mb-1 flex items-center justify-between text-xs">
                <span class="flex items-center gap-1.5 font-medium text-secondary-dark">
                    @if (! empty($row['swatch']))
                        <span class="h-2.5 w-2.5 shrink-0 rounded-sm {{ $barColor }}"></span>
                    @endif
                    {{ $row['label'] }}
                </span>
                <span class="tabular-nums text-secondary">{{ $row['value'] }}{{ $unit }}</span>
            </div>
            <div
                class="h-3 w-full overflow-hidden rounded-full bg-secondary-light"
                title="{{ $row['label'] }}: {{ $row['value'] }}{{ $unit }}{{ isset($row['hint']) ? ' — ' . $row['hint'] : '' }}"
                tabindex="0"
            >
                <div
                    class="h-3 rounded-full {{ $barColor }} transition-all duration-300 ease-out group-hover:brightness-110"
                    style="width: {{ max($pct, 2) }}%"
                ></div>
            </div>
        </div>
    @endforeach
</div>
