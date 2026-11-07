@props(['title' => null, 'celebrate' => false])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}7 KAIH · SKKK Jayapura</title>
    <link rel="icon" href="{{ asset('images/logo-kalam-kudus-new.png') }}">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans" data-celebrate="{{ $celebrate || session('celebrate') ? 'true' : 'false' }}">
    @php($user = auth()->user())

    <header class="relative overflow-hidden bg-gradient-to-br from-brand-800 via-brand-600 to-brand-500 text-white">
        <div class="bg-dots absolute inset-0 opacity-60"></div>
        <div class="absolute -top-24 -right-16 size-72 rounded-full bg-accent-500/25 blur-3xl"></div>

        <nav class="relative mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="grid size-11 place-items-center rounded-2xl bg-white p-1.5 shadow-lg">
                    <img src="{{ asset('images/logo-kalam-kudus-new.png') }}" alt="Logo Kalam Kudus" class="h-full w-full object-contain object-left">
                </span>
                <span class="leading-tight">
                    <span class="block font-display text-lg font-semibold">7 KAIH</span>
                    <span class="block text-xs text-white/70">SKKK Jayapura</span>
                </span>
            </a>

            @auth
                <button type="button" class="rounded-xl p-2 hover:bg-white/10 md:hidden" data-menu-toggle aria-label="Buka menu">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>

                <div class="absolute inset-x-4 top-full z-40 mt-1 hidden flex-col gap-1 rounded-2xl bg-brand-700 p-3 shadow-2xl md:static md:mt-0 md:flex md:flex-row md:items-center md:bg-transparent md:p-0 md:shadow-none" data-menu>
                    @if ($user->canManage())
                        <a href="{{ route('admin.dashboard') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('admin.dashboard')])>📊 Dashboard</a>
                        <a href="{{ route('admin.submissions.index') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('admin.submissions.*')])>📋 Hasil</a>
                        <a href="{{ route('admin.users.index') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('admin.users.*')])>👥 Pengguna</a>
                        <a href="{{ route('admin.classes.index') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('admin.classes.*')])>🏫 Kelas</a>
                        <a href="{{ route('admin.questions.index') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('admin.questions.*')])>❓ Pertanyaan</a>
                    @endif
                    @if ($user->hasSchoolClass())
                        <a href="{{ route('dashboard') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('dashboard', 'submissions.*')])>🧒 {{ $user->canManage() ? 'Kuesioner Saya' : 'Beranda' }}</a>
                    @endif

                    <div class="mt-2 flex items-center gap-3 border-t border-white/10 pt-3 md:mt-0 md:ml-3 md:border-0 md:pt-0">
                        <a href="{{ route('class.edit') }}" class="flex items-center gap-2 rounded-xl py-1 pr-2 hover:bg-white/10" title="Ubah kelas">
                            <x-avatar :user="$user" class="size-9 ring-2 ring-white/40" />
                            <span class="text-left leading-tight md:hidden lg:block">
                                <span class="block max-w-40 truncate text-sm font-semibold">{{ $user->name }}</span>
                                <span class="block text-xs text-white/70">{{ $user->role->label() }}{{ $user->schoolClass ? ' · '.$user->schoolClass->name : '' }}</span>
                            </span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="rounded-xl bg-white/10 px-3 py-2 text-sm font-semibold hover:bg-white/20" title="Keluar">Keluar</button>
                        </form>
                    </div>
                </div>
            @endauth
        </nav>

        @isset($hero)
            <div class="relative mx-auto max-w-6xl px-4 pt-4 pb-24 sm:px-6">
                {{ $hero }}
            </div>
        @else
            <div class="h-16"></div>
        @endisset
    </header>

    <main class="relative mx-auto -mt-16 max-w-6xl px-4 pb-16 sm:px-6">
        <x-flash />
        {{ $slot }}
    </main>

    <footer class="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500">
        <p class="font-semibold text-brand-600">Sekolah Kristen Kalam Kudus Jayapura</p>
        <p class="mt-1 italic">“With love and discipline improve achievement”</p>
    </footer>
</body>
</html>
