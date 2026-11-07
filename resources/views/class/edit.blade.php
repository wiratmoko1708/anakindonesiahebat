<x-layouts.app title="Pilih Kelas">
    <x-slot:hero>
        <div class="text-center">
            <h1 class="font-display text-3xl font-semibold">Kamu di kelas berapa? 🏫</h1>
            <p class="mt-2 text-white/80">Kelas wajib dipilih agar hasil kuesionermu terekap dengan benar.</p>
        </div>
    </x-slot:hero>

    <form method="POST" action="{{ route('class.update') }}" class="card mx-auto max-w-3xl animate-fade-up p-6 sm:p-8">
        @csrf
        @method('PUT')

        <div class="flex items-center gap-3 rounded-2xl bg-brand-50 p-4">
            <x-avatar :user="$user" class="size-12" />
            <div>
                <p class="font-semibold text-brand-800">{{ $user->name }}</p>
                <p class="text-sm text-brand-600">{{ $user->email }}</p>
            </div>
        </div>

        @error('school_class_id')
            <p class="mt-4 rounded-xl bg-rose-50 px-4 py-2 text-sm font-medium text-rose-700">{{ $message }}</p>
        @enderror

        @forelse ($classesByGrade as $grade => $classes)
            <fieldset class="mt-6">
                <legend class="label">Kelas {{ $grade }}</legend>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach ($classes as $class)
                        <label class="class-option cursor-pointer">
                            <input type="radio" name="school_class_id" value="{{ $class->id }}" class="sr-only" @checked(old('school_class_id', $user->school_class_id) == $class->id) required>
                            <span class="class-face block rounded-2xl border-2 border-slate-200 bg-white px-4 py-4 text-center font-display text-lg font-semibold text-slate-700 transition hover:border-brand-300 hover:bg-brand-50">{{ $class->name }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
        @empty
            <p class="mt-6 text-center text-slate-500">Belum ada kelas aktif. Hubungi admin sekolah.</p>
        @endforelse

        <div class="mt-8 flex justify-end">
            <button class="btn-primary px-6 py-3">Simpan Kelas ✓</button>
        </div>
    </form>
</x-layouts.app>
