<x-layouts.app>
    <x-slot:hero>
        <div class="grid items-center gap-10 md:grid-cols-2">
            <div class="animate-fade-up">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold ring-1 ring-white/25">✨ Kuesioner Siswa Kelas X · XI · XII</span>
                <h1 class="mt-4 font-display text-4xl leading-tight font-semibold sm:text-5xl">7 Kebiasaan <span class="text-amber-300">Anak Indonesia</span> Hebat</h1>
                <p class="mt-4 max-w-lg text-white/80">Yuk, cek kebiasaan baikmu! Isi kuesioner singkat (± 2 menit) untuk melihat perkembanganmu menjadi pribadi yang <b>takut akan Tuhan, mandiri, dan berguna bagi dunia</b>.</p>
                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ route('login') }}" class="btn-accent px-6 py-3 text-base">Mulai Isi Kuesioner →</a>
                </div>
            </div>
            <div class="grid grid-cols-4 gap-3 sm:gap-4">
                @foreach (\App\Enums\Habit::cases() as $habit)
                    <div @class(['animate-float rounded-3xl bg-white/10 p-3 text-center ring-1 ring-white/20 backdrop-blur', 'col-start-2' => $loop->index === 4]) style="animation-delay: {{ $loop->index * 0.4 }}s">
                        <div class="text-3xl sm:text-4xl">{{ $habit->emoji() }}</div>
                        <div class="mt-1 text-[11px] leading-tight font-semibold sm:text-xs">{{ $habit->label() }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-slot:hero>

    <section class="card p-6 sm:p-8">
        <div class="grid gap-8 md:grid-cols-3">
            <div class="md:col-span-1">
                <p class="text-xs font-bold tracking-widest text-accent-500 uppercase">Visi Sekolah</p>
                <h2 class="mt-2 font-display text-2xl font-semibold text-brand-700">Manusia utuh yang takut akan Tuhan, mandiri & berguna bagi dunia</h2>
                <p class="mt-3 text-sm text-slate-500">Amsal 1:7 · Efesus 2:19-20 · 1 Korintus 9:19</p>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 md:col-span-2">
                @foreach (\App\Enums\Habit::cases() as $habit)
                    <div class="flex gap-3 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                        <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-gradient-to-br text-xl shadow {{ $habit->gradient() }}">{{ $habit->emoji() }}</span>
                        <div>
                            <p class="font-semibold text-slate-800">{{ $habit->label() }}</p>
                            <p class="text-xs text-slate-500">{{ $habit->tagline() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-3">
        @foreach ([['🔐', 'Login Gmail', 'Masuk aman memakai akun Google/Gmail milikmu.'], ['🏫', 'Pilih Kelas', 'Tentukan kelasmu agar hasil terekap per kelas.'], ['🗓️', config('kaih.max_submissions').'× Pengisian', 'Isi berkala untuk memantau perkembangan, lalu cetak PDF.']] as [$icon, $heading, $text])
            <div class="card p-5">
                <div class="text-3xl">{{ $icon }}</div>
                <p class="mt-2 font-display text-lg font-semibold text-brand-700">{{ $heading }}</p>
                <p class="text-sm text-slate-500">{{ $text }}</p>
            </div>
        @endforeach
    </section>
</x-layouts.app>
