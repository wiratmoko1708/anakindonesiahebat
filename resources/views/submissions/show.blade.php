@php($level = $submission->level())
<x-layouts.app title="Hasil Pengisian #{{ $submission->attempt_number }}">
    <x-slot:hero>
        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm text-white/70">{{ $submission->user->name }} · Kelas {{ $submission->schoolClass?->name ?? '-' }}</p>
                <h1 class="font-display text-3xl font-semibold">Hasil Pengisian ke-{{ $submission->attempt_number }}</h1>
                <p class="text-sm text-white/80">{{ $submission->submitted_at->translatedFormat('l, d F Y · H:i') }} WIT</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('submissions.pdf', $submission) }}" target="_blank" class="btn-accent px-5 py-3">🖨️ Cetak PDF</a>
                <a href="{{ auth()->user()->is($submission->user) ? route('dashboard') : route('admin.submissions.index') }}" class="btn bg-white/15 px-5 py-3 text-white ring-1 ring-white/30 hover:bg-white/25">← Kembali</a>
            </div>
        </div>
    </x-slot:hero>

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="card flex flex-col items-center justify-center p-8 text-center">
            <div class="relative grid size-44 place-items-center rounded-full" style="background: conic-gradient({{ $level['color'] }} {{ $submission->scorePercentage() }}%, #e2e8f0 0)">
                <div class="grid size-36 place-items-center rounded-full bg-white">
                    <div>
                        <p class="font-display text-5xl font-semibold text-slate-900">{{ number_format($submission->average_score, 2) }}</p>
                        <p class="text-xs text-slate-500">dari 4.00</p>
                    </div>
                </div>
            </div>
            <p class="mt-5 text-4xl">{{ $level['emoji'] }}</p>
            <p class="font-display text-2xl font-semibold" style="color: {{ $level['color'] }}">{{ $level['label'] }}</p>
            @if ($submission->reflection)
                <blockquote class="mt-5 rounded-2xl bg-slate-50 p-4 text-left text-sm text-slate-600 italic">“{{ $submission->reflection }}”</blockquote>
            @endif
        </section>

        <section class="card p-6 lg:col-span-2">
            <h2 class="mb-5 font-display text-lg font-semibold text-slate-800">Skor per Kebiasaan</h2>
            <x-habit-bars :habit-averages="$habitScores" />
        </section>
    </div>

    <section class="mt-6 grid gap-4 md:grid-cols-2">
        @foreach ($habitScores as $row)
            <div class="card overflow-hidden">
                <div class="flex items-center gap-3 bg-gradient-to-r px-5 py-3 text-white {{ $row['habit']->gradient() }}">
                    <span class="text-2xl">{{ $row['habit']->emoji() }}</span>
                    <span class="flex-1 font-display font-semibold">{{ $row['habit']->label() }}</span>
                    <span class="rounded-full bg-white/25 px-2.5 py-0.5 font-display text-sm font-semibold">{{ number_format($row['average'], 2) }}</span>
                </div>
                <ul class="divide-y divide-slate-100">
                    @foreach ($row['answers'] as $answer)
                        <li class="flex items-start justify-between gap-3 px-5 py-3 text-sm">
                            <span class="text-slate-600">{{ $answer->question_text }}</span>
                            <span class="shrink-0 rounded-lg bg-slate-100 px-2 py-1 text-xs font-semibold whitespace-nowrap text-slate-700">{{ $scale[$answer->score]['emoji'] }} {{ $scale[$answer->score]['label'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </section>
</x-layouts.app>
