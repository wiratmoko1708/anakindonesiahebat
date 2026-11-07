<x-layouts.app title="Kelas">
    <x-slot:hero>
        <h1 class="font-display text-3xl font-semibold">Kelola Kelas 🏫</h1>
        <p class="mt-1 text-white/80">Kelas aktif muncul di pilihan kelas siswa.</p>
    </x-slot:hero>

    <div class="grid gap-6 lg:grid-cols-3">
        <form method="POST" action="{{ route('admin.classes.store') }}" class="card relative z-10 h-fit p-6">
            @csrf
            <h2 class="mb-4 font-display text-lg font-semibold">Tambah Kelas</h2>
            <label class="label" for="grade">Tingkat</label>
            <select id="grade" name="grade" class="input mb-4">
                @foreach (['X', 'XI', 'XII'] as $grade)
                    <option value="{{ $grade }}" @selected(old('grade') === $grade)>{{ $grade }}</option>
                @endforeach
            </select>
            <label class="label" for="name">Nama Kelas</label>
            <input id="name" name="name" value="{{ old('name') }}" class="input" placeholder="mis. X IPA 1" required>
            @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            <button class="btn-primary mt-5 w-full">+ Tambah</button>
        </form>

        <section class="card relative z-10 overflow-hidden lg:col-span-2">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase">
                    <tr><th class="px-5 py-3">Tingkat & Nama</th><th class="px-5 py-3 text-right">Siswa</th><th class="px-5 py-3 text-right">Pengisian</th><th class="px-5 py-3">Aktif</th><th class="px-5 py-3"></th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($classes as $class)
                        <tr @class(['hover:bg-slate-50', 'opacity-60' => ! $class->is_active])>
                            <td class="px-5 py-3">
                                <form id="class-{{ $class->id }}" method="POST" action="{{ route('admin.classes.update', $class) }}" class="flex gap-2">
                                    @csrf @method('PUT')
                                    <select name="grade" class="input w-20 py-1.5" aria-label="Tingkat">
                                        @foreach (['X', 'XI', 'XII'] as $grade)
                                            <option value="{{ $grade }}" @selected($class->grade === $grade)>{{ $grade }}</option>
                                        @endforeach
                                    </select>
                                    <input name="name" value="{{ $class->name }}" class="input w-36 py-1.5" aria-label="Nama kelas">
                                </form>
                            </td>
                            <td class="px-5 py-3 text-right tabular-nums">{{ $class->users_count }}</td>
                            <td class="px-5 py-3 text-right tabular-nums">{{ $class->submissions_count }}</td>
                            <td class="px-5 py-3">
                                <input type="hidden" name="is_active" value="0" form="class-{{ $class->id }}">
                                <input type="checkbox" name="is_active" value="1" form="class-{{ $class->id }}" @checked($class->is_active) class="size-5 rounded accent-brand-600" aria-label="Aktif">
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex justify-end gap-1.5">
                                    <button form="class-{{ $class->id }}" class="btn-ghost px-2.5 py-1.5 text-xs">Simpan</button>
                                    <form method="POST" action="{{ route('admin.classes.destroy', $class) }}" data-confirm="Hapus kelas {{ $class->name }}?">
                                        @csrf @method('DELETE')
                                        <button class="btn-danger px-2.5 py-1.5 text-xs">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    </div>
</x-layouts.app>
