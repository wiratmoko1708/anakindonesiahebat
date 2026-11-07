@props(['label', 'value', 'icon', 'hint' => null, 'tone' => 'brand'])
@php
    $tones = [
        'brand' => 'from-brand-500 to-brand-700',
        'accent' => 'from-accent-400 to-accent-600',
        'emerald' => 'from-emerald-400 to-teal-600',
        'amber' => 'from-amber-400 to-orange-500',
    ];
@endphp
<div {{ $attributes->merge(['class' => 'card flex items-center gap-4 p-5']) }}>
    <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br text-2xl shadow-lg {{ $tones[$tone] }}">{{ $icon }}</span>
    <div class="min-w-0">
        <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">{{ $label }}</p>
        <p class="font-display text-2xl font-semibold text-slate-900">{{ $value }}</p>
        @if ($hint)
            <p class="truncate text-xs text-slate-500">{{ $hint }}</p>
        @endif
    </div>
</div>
