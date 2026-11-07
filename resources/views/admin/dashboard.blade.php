<x-layouts.app title="Dashboard Admin">
    <x-slot:hero>
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm text-white/70">Panel {{ auth()->user()->role->label() }}</p>
                <h1 class="font-display text-3xl font-semibold sm:text-4xl">Dashboard Pemantauan 7 KAIH 📊</h1>
                <p class="mt-1 text-white/80">Pantau kebiasaan siswa kelas X, XI, dan XII secara real-time.</p>
            </div>
            <a href="{{ route('admin.submissions.report', request()->query()) }}" target="_blank" class="btn-accent px-5 py-3">🖨️ Cetak Laporan PDF</a>
        </div>
    </x-slot:hero>

    <x-admin-filters :filters="$filters" :classes="$classes" :action="route('admin.dashboard')" class="relative z-10 mb-6" />

    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card icon="📝" label="Total Pengisian" :value="number_format($summary['submissions'])" :hint="$summary['respondents'].' siswa berpartisipasi'" />
        <x-stat-card icon="⭐" tone="amber" label="Rata-rata Skor" :value="$summary['average'] !== null ? number_format($summary['average'], 2) : '–'" :hint="$summary['average'] !== null ? \App\Models\Submission::levelFor($summary['average'])['label'] : 'Belum ada data'" />
        <x-stat-card icon="🏆" tone="emerald" label="Tuntas {{ $maxSubmissions }}×" :value="$completedStudents" :hint="'dari '.$studentCount.' siswa terdaftar'" />
        <x-stat-card icon="⏳" tone="accent" label="Belum Mengisi" :value="$notStartedStudents" hint="siswa perlu diingatkan" />
    </section>

    <div class="mt-6 grid gap-6 lg:grid-cols-5">
        <section class="card p-6 lg:col-span-3">
            <h2 class="font-display text-lg font-semibold text-slate-800">Rata-rata per Kebiasaan</h2>
            <p class="mb-5 text-sm text-slate-500">Skala 1 (tidak pernah) – 4 (selalu)</p>
            <x-habit-bars :habit-averages="$habitAverages" />

            @php($ranked = $habitAverages->filter(fn ($row) => $row['average'] !== null)->sortBy('average'))
            @if ($ranked->isNotEmpty())
                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl bg-emerald-50 p-4 ring-1 ring-emerald-100">
                        <p class="text-xs font-bold text-emerald-700 uppercase">💪 Kebiasaan Terkuat</p>
                        <p class="mt-1 font-display text-lg font-semibold text-emerald-900">{{ $ranked->last()['habit']->emoji() }} {{ $ranked->last()['habit']->label() }}</p>
                        <p class="text-xs text-emerald-700">Rata-rata {{ number_format($ranked->last()['average'], 2) }}</p>
                    </div>
                    <div class="rounded-2xl bg-rose-50 p-4 ring-1 ring-rose-100">
                        <p class="text-xs font-bold text-rose-700 uppercase">🎯 Prioritas Pendampingan</p>
                        <p class="mt-1 font-display text-lg font-semibold text-rose-900">{{ $ranked->first()['habit']->emoji() }} {{ $ranked->first()['habit']->label() }}</p>
                        <p class="text-xs text-rose-700">Rata-rata {{ number_format($ranked->first()['average'], 2) }}</p>
                    </div>
                </div>
            @endif
        </section>

        <div class="space-y-6 lg:col-span-2">
            <section class="card p-6">
                <h2 class="font-display text-lg font-semibold text-slate-800">Tren per Pengisian</h2>
                <p class="mb-8 text-sm text-slate-500">Rata-rata skor seluruh siswa</p>
                <x-trend-chart :trend="$trend" />
            </section>

            <section class="card p-6">
                <h2 class="mb-4 font-display text-lg font-semibold text-slate-800">Sebaran Kategori</h2>
                @php($totalLevels = max(1, $levels->sum('count')))
                <div class="mb-4 flex h-4 gap-0.5 overflow-hidden rounded-full bg-slate-100">
                    @foreach ($levels as $level)
                        @if ($level['count'] > 0)
                            <div style="width: {{ $level['count'] / $totalLevels * 100 }}%; background: {{ $level['color'] }}" title="{{ $level['label'] }}: {{ $level['count'] }}"></div>
                        @endif
                    @endforeach
                </div>
                <ul class="space-y-2 text-sm">
                    @foreach ($levels as $level)
                        <li class="flex items-center gap-2">
                            <span class="size-3 rounded-full" style="background: {{ $level['color'] }}"></span>
                            <span class="flex-1 text-slate-600">{{ $level['emoji'] }} {{ $level['label'] }}</span>
                            <span class="font-semibold tabular-nums text-slate-800">{{ $level['count'] }}</span>
                            <span class="w-12 text-right text-xs tabular-nums text-slate-400">{{ round($level['count'] / $totalLevels * 100) }}%</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>
    </div>

    <section class="card mt-6 overflow-hidden">
        <div class="p-6 pb-3">
            <h2 class="font-display text-lg font-semibold text-slate-800">Rekap per Kelas</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase">
                    <tr><th class="px-6 py-3">Kelas</th><th class="px-6 py-3 text-right">Pengisian</th><th class="px-6 py-3">Rata-rata</th><th class="px-6 py-3">Kategori</th><th class="px-6 py-3"></th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($classAverages as $class)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3 font-semibold text-slate-800">{{ $class->name }}</td>
                            <td class="px-6 py-3 text-right tabular-nums">{{ $class->submissions_count }}</td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-2 w-32 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-brand-500" style="width: {{ $class->submissions_avg_average_score / 4 * 100 }}%"></div></div>
                                    <span class="font-semibold tabular-nums">{{ number_format($class->submissions_avg_average_score, 2) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3"><x-level-badge :score="$class->submissions_avg_average_score" /></td>
                            <td class="px-6 py-3 text-right"><a class="text-xs font-semibold text-brand-600 hover:underline" href="{{ route('admin.submissions.report', ['school_class_id' => $class->id]) }}" target="_blank">PDF kelas →</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-slate-500">Belum ada data pengisian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-layouts.app>
