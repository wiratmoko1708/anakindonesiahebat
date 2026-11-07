<?php

namespace App\Actions;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class ResolveGoogleUser
{
    /**
     * Find or create the local account for a Google profile.
     *
     * @throws ValidationException when the email is not allowed to sign in.
     */
    public function handle(SocialiteUser $googleUser): User
    {
        $email = strtolower((string) $googleUser->getEmail());

        if ($email === '' || ! $this->isAllowedEmail($email)) {
            throw ValidationException::withMessages(['email' => 'domain']);
        }

        $user = User::query()
            ->where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first() ?? new User(['role' => Role::User]);

        $user->fill([
            'name' => $googleUser->getName() ?: str($email)->before('@')->headline()->toString(),
            'email' => $email,
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'last_login_at' => now(),
        ]);

        if (in_array($email, config('kaih.super_admin_emails'), true)) {
            $user->role = Role::SuperAdmin;
        }

        $user->email_verified_at ??= now();
        $user->save();

        return $user;
    }

    public function isAllowedEmail(string $email): bool
    {
        if (in_array($email, config('kaih.super_admin_emails'), true)) {
            return true;
        }

        $allowedDomains = config('kaih.allowed_email_domains');

        return $allowedDomains === [] || in_array(str($email)->afterLast('@')->toString(), $allowedDomains, true);
    }
}
