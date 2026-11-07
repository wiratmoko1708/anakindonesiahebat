<x-layouts.app title="Pertanyaan">
    <x-slot:hero>
        <h1 class="font-display text-3xl font-semibold">Instrumen Pertanyaan ❓</h1>
        <p class="mt-1 max-w-2xl text-white/80">Relevan · Jelas · Terukur · Konsisten · Praktis · Etis. Pertanyaan yang sudah dijawab hanya bisa dinonaktifkan agar data lama tetap konsisten.</p>
    </x-slot:hero>

    <form method="POST" action="{{ route('admin.questions.store') }}" class="card relative z-10 mb-6 grid gap-3 p-5 md:grid-cols-[14rem_1fr_auto] md:items-end">
        @csrf
        <div>
            <label class="label" for="habit">Kebiasaan</label>
            <select id="habit" name="habit" class="input">
                @foreach ($habits as $habit)
                    <option value="{{ $habit->value }}" @selected(old('habit') === $habit->value)>{{ $habit->emoji() }} {{ $habit->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label" for="body">Pertanyaan baru</label>
            <input id="body" name="body" value="{{ old('body') }}" class="input" placeholder="Saya …" required>
            @error('body') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>
        <button class="btn-primary">+ Tambah</button>
    </form>

    <div class="grid gap-5 md:grid-cols-2">
        @foreach ($habits as $habit)
            <section class="card overflow-hidden">
                <header class="flex items-center gap-3 bg-gradient-to-r px-5 py-3 text-white {{ $habit->gradient() }}">
                    <span class="text-2xl">{{ $habit->emoji() }}</span>
                    <div>
                        <p class="font-display font-semibold">{{ $habit->label() }}</p>
                        <p class="text-[11px] text-white/85">{{ $habit->mission() }}</p>
                    </div>
                </header>
                <ul class="divide-y divide-slate-100">
                    @forelse ($questionsByHabit[$habit->value] ?? [] as $question)
                        <li @class(['p-4', 'bg-slate-50 opacity-60' => ! $question->is_active])>
                            <form method="POST" action="{{ route('admin.questions.update', $question) }}" class="space-y-2">
                                @csrf @method('PUT')
                                <textarea name="body" rows="2" class="input text-sm" aria-label="Teks pertanyaan">{{ $question->body }}</textarea>
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <label class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" @checked($question->is_active) class="size-4 accent-brand-600"> Aktif
                                        <span class="font-normal text-slate-400">· {{ $question->answers_count }} jawaban</span>
                                    </label>
                                    <div class="flex gap-1.5">
                                        <button class="btn-ghost px-2.5 py-1.5 text-xs">Simpan</button>
                                        <button form="delete-question-{{ $question->id }}" class="btn-danger px-2.5 py-1.5 text-xs">Hapus</button>
                                    </div>
                                </div>
                            </form>
                            <form id="delete-question-{{ $question->id }}" method="POST" action="{{ route('admin.questions.destroy', $question) }}" data-confirm="Hapus pertanyaan ini?">
                                @csrf @method('DELETE')
                            </form>
                        </li>
                    @empty
                        <li class="p-5 text-center text-sm text-slate-400">Belum ada pertanyaan.</li>
                    @endforelse
                </ul>
            </section>
        @endforeach
    </div>
</x-layouts.app>
