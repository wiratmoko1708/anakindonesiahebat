@props(['trend'])
{{-- Column chart of average score per filling (attempt). Hover shows exact value + respondents. --}}
<div class="flex h-48 items-end gap-2 sm:gap-3" role="img" aria-label="Rata-rata skor per pengisian">
    @foreach ($trend as $point)
        @php($height = $point['average'] !== null ? max(6, ($point['average'] / 4) * 100) : 0)
        <div class="group relative flex h-full flex-1 flex-col items-center justify-end gap-2">
            <span class="font-display text-xs font-semibold tabular-nums text-slate-700">{{ $point['average'] !== null ? number_format($point['average'], 2) : '' }}</span>
            <div class="relative w-full max-w-14 flex-1">
                <div class="absolute inset-x-0 bottom-0 rounded-t-lg bg-gradient-to-t from-brand-600 to-brand-400 transition group-hover:from-accent-500 group-hover:to-accent-400" style="height: {{ $height }}%"></div>
                @if ($point['average'] === null)
                    <div class="absolute inset-x-0 bottom-0 h-1 rounded bg-slate-200"></div>
                @endif
            </div>
            <span class="text-xs font-semibold text-slate-500">#{{ $point['attempt'] }}</span>
            <span class="pointer-events-none absolute -top-2 left-1/2 z-10 -translate-x-1/2 -translate-y-full rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs whitespace-nowrap text-white opacity-0 shadow-xl transition group-hover:opacity-100">
                Pengisian ke-{{ $point['attempt'] }}: {{ $point['average'] !== null ? number_format($point['average'], 2) : 'belum ada' }} · {{ $point['responses'] }} respon
            </span>
        </div>
    @endforeach
</div>
