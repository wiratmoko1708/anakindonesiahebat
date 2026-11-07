<x-layouts.app title="Beranda">
    <x-slot:hero>
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm text-white/70">Kelas {{ $user->schoolClass->name }}</p>
                <h1 class="font-display text-3xl font-semibold sm:text-4xl">Halo, {{ str($user->name)->before(' ') }}! 🌟</h1>
                <p class="mt-1 text-white/80">Sudah {{ $summary['submissions'] }} dari {{ $maxSubmissions }} pengisian kuesioner.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @if ($remaining > 0)
                    <a href="{{ route('submissions.create') }}" class="btn-accent px-6 py-3 text-base">✍️ Isi Kuesioner #{{ $summary['submissions'] + 1 }}</a>
                @endif
                @if ($summary['submissions'] > 0)
                    <a href="{{ route('submissions.my-report') }}" target="_blank" class="btn bg-white/15 px-5 py-3 text-white ring-1 ring-white/30 hover:bg-white/25">🖨️ Cetak Rekap PDF</a>
                @endif
            </div>
        </div>
    </x-slot:hero>

    {{-- Kuota pengisian --}}
    <section class="card p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="label">Progres Pengisian</p>
                <p class="font-display text-xl font-semibold text-slate-800">
                    @if ($remaining === 0)
                        Semua pengisian selesai! Kamu luar biasa 🎉
                    @else
                        Sisa {{ $remaining }} kali pengisian lagi
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                @for ($attempt = 1; $attempt <= $maxSubmissions; $attempt++)
                    @php($done = $submissions->firstWhere('attempt_number', $attempt))
                    @if ($done)
                        <a href="{{ route('submissions.show', $done) }}" class="grid size-11 place-items-center rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-600 font-display font-semibold text-white shadow-lg shadow-emerald-500/30 transition hover:-translate-y-0.5" title="Pengisian ke-{{ $attempt }} · {{ $done->submitted_at->translatedFormat('d M Y') }}">✓</a>
                    @else
                        <span @class(['grid size-11 place-items-center rounded-2xl border-2 border-dashed font-display font-semibold', 'border-accent-400 text-accent-500' => $attempt === $submissions->count() + 1, 'border-slate-200 text-slate-300' => $attempt !== $submissions->count() + 1])>{{ $attempt }}</span>
                    @endif
                @endfor
            </div>
        </div>
    </section>

    @if ($summary['submissions'] === 0)
        <section class="card mt-6 p-10 text-center">
            <div class="text-6xl">🚀</div>
            <h2 class="mt-3 font-display text-2xl font-semibold text-brand-700">Belum ada pengisian</h2>
            <p class="mx-auto mt-2 max-w-md text-slate-500">Isi kuesioner pertamamu sekarang. Hanya butuh sekitar 2 menit untuk 14 pertanyaan singkat.</p>
            <a href="{{ route('submissions.create') }}" class="btn-primary mt-6 px-6 py-3">Mulai Sekarang →</a>
        </section>
    @else
        <div class="mt-6 grid gap-6 lg:grid-cols-5">
            <section class="card p-6 lg:col-span-3">
                <div class="flex items-center justify-between">
                    <h2 class="font-display text-lg font-semibold text-slate-800">Rata-rata per Kebiasaan</h2>
                    <x-level-badge :score="$summary['average']" />
                </div>
                <p class="mb-5 text-sm text-slate-500">Dari seluruh pengisianmu · skala 1 (tidak pernah) – 4 (selalu)</p>
                <x-habit-bars :habit-averages="$habitAverages" />
            </section>

            <section class="card p-6 lg:col-span-2">
                <h2 class="font-display text-lg font-semibold text-slate-800">Perkembangan Skor</h2>
                <p class="mb-8 text-sm text-slate-500">Rata-rata skor tiap pengisian</p>
                <x-trend-chart :trend="$trend" />
            </section>
        </div>

        <section class="card mt-6 overflow-hidden">
            <div class="flex items-center justify-between p-6 pb-3">
                <h2 class="font-display text-lg font-semibold text-slate-800">Riwayat Pengisian</h2>
            </div>
            <ul class="divide-y divide-slate-100">
                @foreach ($submissions as $submission)
                    <li class="flex flex-wrap items-center gap-4 px-6 py-4 hover:bg-slate-50">
                        <span class="grid size-11 place-items-center rounded-2xl bg-brand-50 font-display text-lg font-semibold text-brand-600">#{{ $submission->attempt_number }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-800">Pengisian ke-{{ $submission->attempt_number }}</p>
                            <p class="text-xs text-slate-500">{{ $submission->submitted_at->translatedFormat('l, d F Y · H:i') }} WIT</p>
                        </div>
                        <span class="font-display text-xl font-semibold tabular-nums text-slate-800">{{ number_format($submission->average_score, 2) }}</span>
                        <x-level-badge :score="$submission->average_score" />
                        <div class="flex gap-2">
                            <a href="{{ route('submissions.show', $submission) }}" class="btn-ghost px-3 py-2 text-xs">Lihat</a>
                            <a href="{{ route('submissions.pdf', $submission) }}" target="_blank" class="btn-ghost px-3 py-2 text-xs">PDF</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</x-layouts.app>
