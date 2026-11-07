<?php

namespace App\Http\Controllers\Auth;

use App\Actions\ResolveGoogleUser;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Throwable;

/**
 * Google login.
 *
 * Google does not accept `.test` hosts as redirect URIs, so locally the OAuth
 * round-trip runs on the callback host (e.g. http://127.0.0.1/kalkud3.test),
 * then hands the login back to APP_URL through a single-use token.
 */
class GoogleController extends Controller
{
    private const HANDOFF_CACHE_PREFIX = 'google-login-handoff:';

    public function redirect(Request $request): RedirectResponse|SymfonyRedirectResponse
    {
        if (blank(config('services.google.client_id')) || blank(config('services.google.client_secret'))) {
            return $this->failed('not_configured');
        }

        $callbackBase = $this->callbackBaseUrl();

        if ($callbackBase !== null && ! $request->boolean('on_callback_host') && rtrim($callbackBase, '/') !== $this->appUrl()) {
            return redirect()->away($callbackBase.'/auth/google/redirect?on_callback_host=1');
        }

        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function callback(Request $request, ResolveGoogleUser $resolveGoogleUser): RedirectResponse
    {
        if ($request->filled('error')) {
            return $this->failed('denied');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            Log::warning('Google login failed', ['message' => $exception->getMessage()]);

            return $this->failed('failed');
        }

        try {
            $user = $resolveGoogleUser->handle($googleUser);
        } catch (ValidationException) {
            return $this->failed('domain');
        }

        $token = Str::random(64);
        Cache::put(self::HANDOFF_CACHE_PREFIX.$token, $user->id, now()->addMinute());

        return redirect()->away($this->appUrl().'/auth/google/complete?token='.$token);
    }

    public function complete(Request $request): RedirectResponse
    {
        $userId = Cache::pull(self::HANDOFF_CACHE_PREFIX.$request->string('token'));
        $user = $userId ? User::find($userId) : null;

        if ($user === null) {
            return $this->failed('expired');
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended($user->canManage() ? route('admin.dashboard') : route('dashboard'))
            ->with('status', 'Selamat datang, '.$user->name.'! 👋');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Kamu sudah keluar. Sampai jumpa lagi! 👋');
    }

    private function failed(string $reason): RedirectResponse
    {
        return redirect()->away($this->appUrl().'/login?error='.$reason);
    }

    private function appUrl(): string
    {
        return rtrim((string) config('app.url'), '/');
    }

    /**
     * The base URL that hosts the OAuth callback, when it is configured as an absolute URL.
     */
    private function callbackBaseUrl(): ?string
    {
        $redirectUri = (string) config('services.google.redirect');

        if (! str_starts_with($redirectUri, 'http')) {
            return null;
        }

        return Str::beforeLast($redirectUri, '/auth/google/callback');
    }
}
