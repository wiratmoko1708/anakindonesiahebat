<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Local-only login for trying the app before Google OAuth credentials exist.
 */
class DevLoginController extends Controller
{
    public static function isEnabled(): bool
    {
        return app()->environment('local') && config('kaih.dev_login');
    }

    public function __invoke(Request $request): RedirectResponse
    {
        abort_unless(self::isEnabled(), 404);

        $validated = $request->validate([
            'role' => ['required', Rule::enum(Role::class)],
        ]);

        $role = Role::from($validated['role']);

        $user = User::firstOrCreate(
            ['email' => "demo.{$role->value}@gmail.com"],
            ['name' => 'Demo '.$role->label(), 'role' => $role, 'email_verified_at' => now()],
        );

        $user->update(['last_login_at' => now()]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->to($user->canManage() ? route('admin.dashboard') : route('dashboard'))
            ->with('status', 'Masuk sebagai akun demo '.$role->label().'.');
    }
}
