@php
    $errorMessages = [
        'domain' => 'Gunakan akun '.(config('kaih.allowed_email_domains') ? '@'.implode(' / @', config('kaih.allowed_email_domains')) : 'Google').' untuk masuk.',
        'denied' => 'Login dibatalkan. Silakan coba lagi.',
        'failed' => 'Login Google gagal. Silakan coba lagi.',
        'expired' => 'Sesi login kedaluwarsa. Silakan coba lagi.',
        'not_configured' => 'Login Google belum dikonfigurasi. Isi GOOGLE_CLIENT_ID & GOOGLE_CLIENT_SECRET di file .env.',
    ];
    $error = $errorMessages[request('error')] ?? null;
@endphp
<x-layouts.app title="Masuk">
    <x-slot:hero>
        <div class="text-center">
            <h1 class="font-display text-3xl font-semibold sm:text-4xl">Halo, Anak Hebat! 👋</h1>
            <p class="mt-2 text-white/80">Masuk untuk mengisi kuesioner 7 Kebiasaan Anak Indonesia Hebat.</p>
        </div>
    </x-slot:hero>

    <div class="mx-auto max-w-md">
        <div class="card animate-fade-up p-8 text-center">
            <img src="{{ asset('images/logo-kalam-kudus-new.png') }}" alt="Sekolah Kristen Kalam Kudus Jayapura" class="mx-auto h-14">

            @if ($error)
                <div class="mt-6 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 ring-1 ring-rose-200">{{ $error }}</div>
            @endif

            <a href="{{ route('google.redirect') }}" class="btn-ghost mt-6 w-full py-3.5 text-base shadow-md">
                <svg class="size-5" viewBox="0 0 48 48" aria-hidden="true"><path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.8 1.2 7.9 3.1l5.7-5.7C34 6.1 29.3 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.4-.4-3.5z"/><path fill="#FF3D00" d="m6.3 14.7 6.6 4.8C14.7 15.1 19 12 24 12c3.1 0 5.8 1.2 7.9 3.1l5.7-5.7C34 6.1 29.3 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/><path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.2 35.1 26.7 36 24 36c-5.2 0-9.6-3.3-11.3-8l-6.5 5C9.5 39.6 16.2 44 24 44z"/><path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.2-2.2 4.2-4.1 5.6l6.2 5.2C37 39.2 44 34 44 24c0-1.3-.1-2.4-.4-3.5z"/></svg>
                Masuk dengan Gmail
            </a>

            <p class="mt-4 text-xs text-slate-500">Kami hanya menggunakan nama, email, dan foto profil Google kamu. Data jawaban tidak ditampilkan ke publik.</p>

            @if (\App\Http\Controllers\Auth\DevLoginController::isEnabled())
                <div class="mt-8 rounded-2xl border-2 border-dashed border-amber-300 bg-amber-50 p-4 text-left">
                    <p class="text-xs font-bold text-amber-700 uppercase">🧪 Mode uji coba (lokal saja)</p>
                    <p class="mt-1 text-xs text-amber-700">Masuk tanpa Google untuk mencoba aplikasi. Matikan dengan <code>KAIH_DEV_LOGIN=false</code>.</p>
                    <div class="mt-3 grid grid-cols-3 gap-2">
                        @foreach (\App\Enums\Role::cases() as $role)
                            <form method="POST" action="{{ route('dev-login') }}">
                                @csrf
                                <input type="hidden" name="role" value="{{ $role->value }}">
                                <button class="btn-ghost w-full px-2 py-2 text-xs">{{ $role->label() }}</button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
