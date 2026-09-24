{{-- Expects $type (ReportType) and $chart (array with total + points) --}}
@php
    $points = $chart['points'];
    $max = max(1, max(array_column($points, 'value') ?: [0]));
    $sum = array_sum(array_column($points, 'value'));
@endphp
@if ($type->chart() === 'pie')
    @php
        $offset = 0;
        $stops = [];
        foreach ($points as $point) {
            $share = $sum > 0 ? $point['value'] / $sum * 100 : 0;
            $stops[] = $point['color'].' '.$offset.'% '.($offset + $share).'%';
            $offset += $share;
        }
        $gradient = $sum > 0 ? 'conic-gradient('.implode(', ', $stops).')' : '#e2e8f0';
    @endphp
    <div class="flex flex-wrap items-center gap-6">
        <div class="relative size-36 shrink-0 rounded-full" style="background: {{ $gradient }}">
            <div class="absolute inset-7 flex flex-col items-center justify-center rounded-full bg-white">
                <span class="text-xl font-semibold">{{ number_format($sum) }}</span>
                <span class="text-[11px] text-slate-500">total</span>
            </div>
        </div>
        <ul class="space-y-2 text-sm">
            @foreach ($points as $point)
                <li class="flex items-center gap-2">
                    <span class="size-3 rounded-sm" style="background: {{ $point['color'] }}"></span>
                    <span class="text-slate-700">{{ $point['label'] }}</span>
                    <span class="font-semibold">{{ $point['display'] }}</span>
                    <span class="text-slate-500">({{ $sum > 0 ? round($point['value'] / $sum * 100) : 0 }}%)</span>
                </li>
            @endforeach
        </ul>
    </div>
@elseif ($type->chart() === 'bar')
    <ul class="space-y-2.5">
        @foreach ($points as $point)
            <li class="grid grid-cols-[8.5rem_1fr_2.5rem] items-center gap-3 text-sm">
                <span class="truncate text-slate-700" title="{{ $point['label'] }}">{{ $point['label'] }}</span>
                <span class="h-5 overflow-hidden rounded-sm bg-slate-100">
                    <span class="block h-full rounded-sm" style="width: {{ $point['value'] / $max * 100 }}%; background: {{ $point['color'] }}"></span>
                </span>
                <span class="text-right font-semibold">{{ $point['display'] }}</span>
            </li>
        @endforeach
    </ul>
@else
    <div class="flex h-44 items-end gap-3 border-b border-slate-200 pb-0">
        @foreach ($points as $point)
            <div class="flex h-full flex-1 flex-col items-center justify-end gap-1">
                <span class="text-xs font-semibold text-slate-700">{{ $point['display'] }}</span>
                <span class="w-full max-w-14 rounded-t-sm" style="height: {{ max(2, $point['value'] / $max * 100) }}%; background: {{ $point['color'] }}; opacity: {{ $point['value'] > 0 ? 1 : 0.25 }}"></span>
            </div>
        @endforeach
    </div>
    <div class="mt-1.5 flex gap-3">
        @foreach ($points as $point)
            <span class="flex-1 truncate text-center text-xs text-slate-600">{{ $point['label'] }}</span>
        @endforeach
    </div>
@endif
