@props(['score'])
@php($level = \App\Models\Submission::levelFor((float) $score))
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold '.$level['classes']]) }}>
    {{ $level['emoji'] }} {{ $level['label'] }}
</span>
