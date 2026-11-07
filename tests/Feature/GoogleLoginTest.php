<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.url' => 'http://kalkud3.test',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
            'kaih.allowed_email_domains' => ['gmail.com'],
            'kaih.super_admin_emails' => ['kepsek@gmail.com'],
        ]);
    }

    public function test_gmail_user_is_created_and_handed_back_to_the_app_url(): void
    {
        $this->fakeGoogleUser('siswa@gmail.com');

        $response = $this->get('/auth/google/callback');

        $response->assertRedirectContains('http://kalkud3.test/auth/google/complete?token=');
        $this->assertDatabaseHas('users', ['email' => 'siswa@gmail.com', 'role' => Role::User->value]);

        $this->get(str($response->headers->get('Location'))->after('kalkud3.test')->toString())
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs(User::firstWhere('email', 'siswa@gmail.com'));
    }

    public function test_handoff_token_can_only_be_used_once(): void
    {
        $this->fakeGoogleUser('siswa@gmail.com');
        $completeUrl = str($this->get('/auth/google/callback')->headers->get('Location'))->after('kalkud3.test')->toString();

        $this->get($completeUrl);
        auth()->logout();

        $this->get($completeUrl)->assertRedirect('http://kalkud3.test/login?error=expired');
        $this->assertGuest();
    }

    public function test_non_gmail_account_is_rejected(): void
    {
        $this->fakeGoogleUser('guru@yahoo.com');

        $this->get('/auth/google/callback')->assertRedirect('http://kalkud3.test/login?error=domain');
        $this->assertDatabaseMissing('users', ['email' => 'guru@yahoo.com']);
    }

    public function test_configured_email_becomes_super_admin(): void
    {
        $this->fakeGoogleUser('kepsek@gmail.com');

        $this->get('/auth/google/callback');

        $this->assertSame(Role::SuperAdmin, User::firstWhere('email', 'kepsek@gmail.com')->role);
    }

    public function test_denied_consent_returns_to_login(): void
    {
        $this->get('/auth/google/callback?error=access_denied')
            ->assertRedirect('http://kalkud3.test/login?error=denied');
    }

    public function test_redirect_hops_to_the_callback_host_first(): void
    {
        config(['services.google.client_id' => 'id', 'services.google.client_secret' => 'secret']);

        $this->get('/auth/google/redirect')
            ->assertRedirect('http://localhost/auth/google/redirect?on_callback_host=1');
    }

    private function fakeGoogleUser(string $email): void
    {
        Socialite::fake('google', (new SocialiteUser)->map([
            'id' => (string) crc32($email),
            'name' => 'Pengguna Uji',
            'email' => $email,
            'avatar' => 'https://example.com/avatar.png',
        ]));
    }
}
