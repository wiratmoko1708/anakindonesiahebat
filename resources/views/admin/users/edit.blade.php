@php($actor = auth()->user())
<x-layouts.app title="Edit Pengguna">
    <x-slot:hero>
        <h1 class="font-display text-3xl font-semibold">Edit Pengguna ✏️</h1>
        <p class="mt-1 text-white/80">Ubah data akun {{ $user->email }}@if ($user->is($actor)) (akunmu sendiri)@endif.</p>
    </x-slot:hero>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="card relative z-10 mx-auto grid max-w-2xl gap-4 p-6">
        @csrf @method('PATCH')

        <div class="flex items-center gap-3">
            <x-avatar :user="$user" class="size-12" />
            <div class="min-w-0">
                <p class="truncate font-semibold text-slate-800">{{ $user->name }}</p>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $user->role->badgeClasses() }}">{{ $user->role->label() }}</span>
            </div>
        </div>

        <div>
            <label class="label" for="name">Nama</label>
            <input id="name" name="name" value="{{ old('name', $user->name) }}" class="input" required>
            @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label" for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" class="input" required>
            @error('email') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label" for="role">Role</label>
                <select id="role" name="role" class="input" @disabled($user->is($actor))>
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}" @selected(old('role', $user->role->value) === $role->value)>{{ $role->label() }}</option>
                    @endforeach
                </select>
                @if ($user->is($actor))
                    <p class="mt-1 text-xs text-slate-400">Role akunmu sendiri tidak dapat diubah.</p>
                @endif
                @error('role') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label" for="school_class_id">Kelas</label>
                <select id="school_class_id" name="school_class_id" class="input">
                    <option value="">— Belum —</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" @selected(old('school_class_id', $user->school_class_id) == $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
                @error('school_class_id') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <fieldset class="rounded-2xl border border-slate-200 p-4">
            <legend class="px-1 text-xs font-semibold tracking-wide text-slate-500 uppercase">Reset Password</legend>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="password">Password Baru</label>
                    <input id="password" type="password" name="password" class="input" placeholder="Min. 8 karakter" autocomplete="new-password">
                    @error('password') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label" for="password_confirmation">Ulangi Password Baru</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="input" autocomplete="new-password">
                </div>
            </div>
            <p class="mt-2 text-xs text-slate-400">Kosongkan jika password tidak ingin diubah.</p>
        </fieldset>

        <div class="flex items-center gap-2">
            <button class="btn-primary">Simpan Perubahan</button>
            <a href="{{ route('admin.users.index') }}" class="btn-ghost px-4 py-2">Batal</a>
        </div>
    </form>
</x-layouts.app>
