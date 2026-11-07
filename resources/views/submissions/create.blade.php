@php($habits = \App\Enums\Habit::cases())
<x-layouts.app title="Isi Kuesioner">
    <x-slot:hero>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm text-white/70">Pengisian ke-{{ $attemptNumber }} dari {{ $maxSubmissions }} · Kelas {{ $user->schoolClass->name }}</p>
                <h1 class="font-display text-3xl font-semibold">Kuesioner 7 KAIH ✍️</h1>
                <p class="mt-1 max-w-xl text-sm text-white/80">{{ config('kaih.period_hint') }}</p>
            </div>
            <div class="w-full sm:w-64">
                <div class="mb-1 flex justify-between text-xs font-semibold"><span data-answered-label>0 terjawab</span><span data-progress-label>0%</span></div>
                <div class="h-3 overflow-hidden rounded-full bg-white/20"><div class="h-full w-0 rounded-full bg-gradient-to-r from-amber-300 to-accent-400 transition-all duration-500" data-progress-bar></div></div>
            </div>
        </div>
    </x-slot:hero>

    {{-- Step navigator --}}
    <nav class="card sticky top-2 z-30 mb-6 flex justify-between gap-1 overflow-x-auto p-2" aria-label="Langkah kuesioner">
        @foreach ($habits as $habit)
            <button type="button" data-step-dot data-state="todo" class="group flex min-w-12 flex-1 flex-col items-center gap-1 rounded-2xl px-1 py-2 text-[11px] font-semibold text-slate-400 transition hover:bg-slate-50 data-[state=current]:bg-brand-50 data-[state=current]:text-brand-700 data-[state=done]:text-emerald-600">
                <span class="grid size-9 place-items-center rounded-xl bg-slate-100 text-lg transition group-data-[state=current]:scale-110 group-data-[state=current]:bg-white group-data-[state=current]:shadow group-data-[state=done]:bg-emerald-50">{{ $habit->emoji() }}</span>
                <span class="hidden leading-tight sm:block">{{ str($habit->label())->before(' &') }}</span>
            </button>
        @endforeach
        <button type="button" data-step-dot data-state="todo" class="group flex min-w-12 flex-1 flex-col items-center gap-1 rounded-2xl px-1 py-2 text-[11px] font-semibold text-slate-400 hover:bg-slate-50 data-[state=current]:bg-brand-50 data-[state=current]:text-brand-700">
            <span class="grid size-9 place-items-center rounded-xl bg-slate-100 text-lg">💬</span>
            <span class="hidden sm:block">Refleksi</span>
        </button>
    </nav>

    @if ($errors->any())
        <div class="mb-6 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 ring-1 ring-rose-200">Masih ada pertanyaan yang belum dijawab. Periksa kembali ya! 🙏</div>
    @endif

    <form method="POST" action="{{ route('submissions.store') }}" data-questionnaire>
        @csrf

        @foreach ($habits as $habit)
            @continue(! $questionsByHabit->has($habit->value))
            <section data-step class="card animate-fade-up scroll-mt-28 overflow-hidden">
                <header class="relative overflow-hidden bg-gradient-to-br p-6 text-white {{ $habit->gradient() }}">
                    <div class="bg-dots absolute inset-0"></div>
                    <div class="relative flex items-center gap-4">
                        <span class="grid size-16 shrink-0 animate-float place-items-center rounded-3xl bg-white/20 text-4xl ring-1 ring-white/30">{{ $habit->emoji() }}</span>
                        <div>
                            <p class="text-xs font-bold tracking-widest text-white/80 uppercase">Kebiasaan {{ $loop->iteration }} dari 7</p>
                            <h2 class="font-display text-2xl font-semibold">{{ $habit->label() }}</h2>
                            <p class="text-sm text-white/85">{{ $habit->tagline() }}</p>
                        </div>
                    </div>
                    <p class="relative mt-4 inline-block rounded-full bg-black/15 px-3 py-1 text-[11px] font-semibold">✝️ {{ $habit->mission() }}</p>
                </header>

                <div class="space-y-5 p-5 sm:p-6">
                    @foreach ($questionsByHabit[$habit->value] as $question)
                        @php($fieldError = $errors->has("answers.{$question->id}"))
                        <fieldset data-question @class(['rounded-2xl bg-slate-50 p-4 transition sm:p-5', 'has-error ring-2 ring-accent-400' => $fieldError])>
                            <legend class="sr-only">{{ $question->body }}</legend>
                            <p class="mb-4 flex gap-3 font-semibold text-slate-800" aria-hidden="true">
                                <span class="grid size-7 shrink-0 place-items-center rounded-lg bg-white text-sm text-brand-600 shadow-sm">{{ $loop->iteration }}</span>
                                <span>{{ $question->body }}</span>
                            </p>
                            <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-4">
                                @foreach ($scale as $value => $option)
                                    <label class="scale-option cursor-pointer">
                                        <input type="radio" class="sr-only" name="answers[{{ $question->id }}]" value="{{ $value }}" @checked(old("answers.{$question->id}") == $value) required>
                                        <span class="scale-face flex h-full flex-col items-center gap-1 rounded-2xl border-2 border-slate-200 bg-white px-2 py-3 text-center transition hover:-translate-y-0.5 hover:border-brand-300">
                                            <span class="text-2xl">{{ $option['emoji'] }}</span>
                                            <span class="text-sm font-bold">{{ $option['label'] }}</span>
                                            <span class="text-[11px] opacity-70">{{ $option['hint'] }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                    @endforeach
                </div>

                <footer class="flex justify-between gap-3 border-t border-slate-100 bg-slate-50/60 px-6 py-4">
                    @if (! $loop->first)
                        <button type="button" class="btn-ghost" data-prev>← Sebelumnya</button>
                    @else
                        <span></span>
                    @endif
                    <button type="button" class="btn-primary" data-next>Lanjut →</button>
                </footer>
            </section>
        @endforeach

        <section data-step class="card animate-fade-up scroll-mt-28 overflow-hidden">
            <header class="bg-gradient-to-br from-brand-600 to-brand-800 p-6 text-white">
                <div class="flex items-center gap-4">
                    <span class="grid size-16 place-items-center rounded-3xl bg-white/15 text-4xl">💬</span>
                    <div>
                        <h2 class="font-display text-2xl font-semibold">Refleksi Singkat</h2>
                        <p class="text-sm text-white/80">Opsional · Kebiasaan mana yang ingin kamu tingkatkan?</p>
                    </div>
                </div>
            </header>
            <div class="p-6">
                <textarea name="reflection" rows="4" maxlength="500" class="input" placeholder="Contoh: Minggu ini saya ingin tidur lebih cepat dan mengurangi main HP sebelum tidur.">{{ old('reflection') }}</textarea>
                @error('reflection') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                <p class="mt-4 rounded-2xl bg-amber-50 p-4 text-sm text-amber-800">🙏 Jawablah dengan jujur. Tidak ada jawaban benar atau salah — hasil ini membantu sekolah mendampingimu bertumbuh.</p>
            </div>
            <footer class="flex justify-between gap-3 border-t border-slate-100 bg-slate-50/60 px-6 py-4">
                <button type="button" class="btn-ghost" data-prev>← Sebelumnya</button>
                <button type="submit" class="btn-accent px-6">Kirim Jawaban 🚀</button>
            </footer>
        </section>
    </form>
</x-layouts.app>
