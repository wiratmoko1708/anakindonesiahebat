@props(['habitAverages'])
{{-- Horizontal bars: one per habit, score 1–4. Values are always printed next to the bar. --}}
<ul class="space-y-3.5">
    @foreach ($habitAverages as $row)
        @php($average = $row['average'])
        <li class="group" title="{{ $row['habit']->label() }}: {{ $average !== null ? number_format($average, 2) : 'belum ada data' }}">
            <div class="mb-1 flex items-center justify-between gap-2 text-sm">
                <span class="flex items-center gap-2 font-semibold text-slate-700">
                    <span class="text-lg">{{ $row['habit']->emoji() }}</span>{{ $row['habit']->label() }}
                </span>
                <span class="font-display font-semibold tabular-nums text-slate-900">{{ $average !== null ? number_format($average, 2) : '–' }}<span class="text-xs font-normal text-slate-400"> / 4</span></span>
            </div>
            <div class="h-3 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-gradient-to-r transition-all duration-700 group-hover:brightness-110 {{ $row['habit']->gradient() }}" style="width: {{ $average !== null ? ($average / 4) * 100 : 0 }}%"></div>
            </div>
        </li>
    @endforeach
</ul>
