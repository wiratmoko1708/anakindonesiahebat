@php($actor = auth()->user())
<x-layouts.app title="Pengguna">
    <x-slot:hero>
        <h1 class="font-display text-3xl font-semibold">Kelola Pengguna 👥</h1>
        <p class="mt-1 text-white/80">{{ $actor->isSuperAdmin() ? 'Atur role dan kelas setiap pengguna.' : 'Atur kelas siswa. Perubahan role hanya oleh Super Admin.' }}</p>
    </x-slot:hero>

    @if ($actor->isSuperAdmin())
        <details class="card group relative z-10 mb-6" @if ($errors->any() && ! session('status')) open @endif>
            <summary class="flex cursor-pointer items-center justify-between gap-3 p-4 font-display text-lg font-semibold select-none">
                <span>➕ Tambah Pengguna</span>
                <span class="text-sm font-normal text-slate-400 transition group-open:rotate-45">＋</span>
            </summary>
            <form method="POST" action="{{ route('admin.users.store') }}" class="grid gap-3 border-t border-slate-100 p-4 sm:grid-cols-2 lg:grid-cols-3">
                @csrf
                <div>
                    <label class="label" for="new-name">Nama</label>
                    <input id="new-name" name="name" value="{{ old('name') }}" class="input" placeholder="Nama lengkap" required>
                    @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label" for="new-email">Email</label>
                    <input id="new-email" type="email" name="email" value="{{ old('email') }}" class="input" placeholder="nama@email.com" required>
                    @error('email') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label" for="new-role">Role</label>
                    <select id="new-role" name="role" class="input">
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}" @selected(old('role', 'user') === $role->value)>{{ $role->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label" for="new-class">Kelas <span class="font-normal text-slate-400">(opsional)</span></label>
                    <select id="new-class" name="school_class_id" class="input">
                        <option value="">— Belum —</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected(old('school_class_id') == $class->id)>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label" for="new-password">Password</label>
                    <input id="new-password" type="password" name="password" class="input" placeholder="Min. 8 karakter" required autocomplete="new-password">
                    @error('password') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label" for="new-password-confirmation">Ulangi Password</label>
                    <input id="new-password-confirmation" type="password" name="password_confirmation" class="input" required autocomplete="new-password">
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <button class="btn-primary">+ Tambah Pengguna</button>
                </div>
            </form>
        </details>
    @endif

    <form method="GET" class="card relative z-10 mb-6 flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-48 flex-1">
            <label class="label" for="search">Cari</label>
            <input id="search" type="search" name="search" value="{{ $filters['search'] }}" class="input" placeholder="Nama atau email…">
        </div>
        <div>
            <label class="label" for="role">Role</label>
            <select id="role" name="role" class="input" data-autosubmit>
                <option value="">Semua role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->value }}" @selected($filters['role'] === $role->value)>{{ $role->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label" for="class">Kelas</label>
            <select id="class" name="school_class_id" class="input" data-autosubmit>
                <option value="">Semua kelas</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}" @selected($filters['school_class_id'] === $class->id)>{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn-primary">Cari</button>
    </form>

    <section class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold tracking-wide text-slate-500 uppercase">
                    <tr><th class="px-5 py-3">Pengguna</th><th class="px-5 py-3">Role</th><th class="px-5 py-3">Kelas</th><th class="px-5 py-3">Pengisian</th><th class="px-5 py-3">Login Terakhir</th><th class="px-5 py-3"></th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        @php($canEditClass = $actor->isSuperAdmin() || ! $user->isSuperAdmin())
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <x-avatar :user="$user" class="size-9" />
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-slate-800">{{ $user->name }} @if ($user->is($actor))<span class="text-xs text-slate-400">(kamu)</span>@endif</p>
                                        <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                @if ($actor->isSuperAdmin() && ! $user->is($actor))
                                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                                        @csrf @method('PATCH')
                                        <select name="role" class="input w-36 py-1.5" data-autosubmit aria-label="Role {{ $user->name }}">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->value }}" @selected($user->role === $role)>{{ $role->label() }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                @else
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $user->role->badgeClasses() }}">{{ $user->role->label() }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                @if ($canEditClass)
                                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                                        @csrf @method('PATCH')
                                        <select name="school_class_id" class="input w-32 py-1.5" data-autosubmit aria-label="Kelas {{ $user->name }}">
                                            <option value="">— Belum —</option>
                                            @foreach ($classes as $class)
                                                <option value="{{ $class->id }}" @selected($user->school_class_id === $class->id)>{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                @else
                                    {{ $user->schoolClass?->name ?? '—' }}
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-20 overflow-hidden rounded-full bg-slate-100"><div @class(['h-full rounded-full', 'bg-emerald-500' => $user->submissions_count >= $maxSubmissions, 'bg-brand-500' => $user->submissions_count < $maxSubmissions]) style="width: {{ min(100, $user->submissions_count / $maxSubmissions * 100) }}%"></div></div>
                                    <span class="text-xs font-semibold tabular-nums">{{ $user->submissions_count }}/{{ $maxSubmissions }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-xs whitespace-nowrap text-slate-500">{{ $user->last_login_at?->diffForHumans() ?? '—' }}</td>
                            <td class="px-5 py-3 text-right">
                                @if ($actor->isSuperAdmin())
                                    <div class="flex justify-end gap-1.5">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn-ghost px-2.5 py-1.5 text-xs">Edit</a>
                                        @if (! $user->is($actor))
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" data-confirm="Hapus akun {{ $user->email }} beserta seluruh jawabannya? Tindakan ini tidak dapat dibatalkan.">
                                                @csrf @method('DELETE')
                                                <button class="btn-danger px-2.5 py-1.5 text-xs">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">Tidak ada pengguna.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">{{ $users->links() }}</div>
        @endif
    </section>
</x-layouts.app>
