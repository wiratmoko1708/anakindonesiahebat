<x-layouts.app title="Hasil Kuesioner">
    <x-slot:hero>
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="font-display text-3xl font-semibold">Hasil Kuesioner 📋</h1>
                <p class="mt-1 text-white/80">{{ $submissions->total() }} data pengisian ditemukan.</p>
            </div>
            <a href="{{ route('admin.submissions.report', request()->query()) }}" target="_blank" class="btn-accent px-5 py-3">🖨️ Cetak PDF (sesuai filter)</a>
        </div>
    </x-slot:hero>

    <x-admin-filters :filters="$filters" :classes="$classes" :action="route('admin.submissions.index')" show-search show-attempt class="relative z-10 mb-6" />

    <section class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase">
                    <tr><th class="px-5 py-3">Siswa</th><th class="px-5 py-3">Kelas</th><th class="px-5 py-3 text-center">Ke-</th><th class="px-5 py-3">Tanggal</th><th class="px-5 py-3 text-right">Skor</th><th class="px-5 py-3">Kategori</th><th class="px-5 py-3 text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($submissions as $submission)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <x-avatar :user="$submission->user" class="size-9" />
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-slate-800">{{ $submission->user->name }}</p>
                                        <p class="truncate text-xs text-slate-500">{{ $submission->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 font-semibold">{{ $submission->schoolClass?->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-center"><span class="rounded-lg bg-brand-50 px-2 py-1 font-display font-semibold text-brand-600">{{ $submission->attempt_number }}/{{ $maxSubmissions }}</span></td>
                            <td class="px-5 py-3 whitespace-nowrap text-slate-500">{{ $submission->submitted_at->translatedFormat('d M Y, H:i') }}</td>
                            <td class="px-5 py-3 text-right font-display text-base font-semibold tabular-nums">{{ number_format($submission->average_score, 2) }}</td>
                            <td class="px-5 py-3"><x-level-badge :score="$submission->average_score" /></td>
                            <td class="px-5 py-3">
                                <div class="flex justify-end gap-1.5">
                                    <a href="{{ route('submissions.show', $submission) }}" class="btn-ghost px-2.5 py-1.5 text-xs">Detail</a>
                                    <a href="{{ route('submissions.pdf', $submission) }}" target="_blank" class="btn-ghost px-2.5 py-1.5 text-xs">PDF</a>
                                    @can('delete', $submission)
                                        <form method="POST" action="{{ route('admin.submissions.destroy', $submission) }}" data-confirm="Hapus pengisian ini? Kuota siswa akan dikembalikan 1 kali.">
                                            @csrf @method('DELETE')
                                            <button class="btn-danger px-2.5 py-1.5 text-xs">Hapus</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-12 text-center text-slate-500">Belum ada data yang cocok dengan filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($submissions->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">{{ $submissions->links() }}</div>
        @endif
    </section>
</x-layouts.app>
