<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_a_user(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->post(route('admin.users.store'), [
            'name' => 'Siswa Baru',
            'email' => 'siswa.baru@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => Role::User->value,
            'school_class_id' => null,
        ])->assertRedirect();

        $user = User::where('email', 'siswa.baru@example.com')->firstOrFail();
        $this->assertSame(Role::User, $user->role);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue(Hash::check('rahasia123', $user->password));
    }

    public function test_store_rejects_duplicate_email(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $existing = User::factory()->create();

        $this->actingAs($superAdmin)->post(route('admin.users.store'), [
            'name' => 'Duplikat',
            'email' => $existing->email,
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => Role::User->value,
        ])->assertSessionHasErrors('email');
    }

    public function test_admin_cannot_create_a_user(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Siswa Baru',
            'email' => 'siswa.baru@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => Role::User->value,
        ])->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'siswa.baru@example.com']);
    }

    public function test_super_admin_can_open_the_edit_page(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $student = User::factory()->create();

        $this->actingAs($superAdmin)->get(route('admin.users.edit', $student))->assertOk();
        $this->actingAs(User::factory()->admin()->create())->get(route('admin.users.edit', $student))->assertForbidden();
    }

    public function test_super_admin_can_update_name_email_and_password(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $student = User::factory()->create(['email_verified_at' => null]);
        $oldHash = $student->password;

        $this->actingAs($superAdmin)->patch(route('admin.users.update', $student), [
            'name' => 'Nama Baru',
            'email' => 'email.baru@example.com',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ])->assertRedirect();

        $student->refresh();
        $this->assertSame('Nama Baru', $student->name);
        $this->assertSame('email.baru@example.com', $student->email);
        $this->assertNotNull($student->email_verified_at);
        $this->assertTrue(Hash::check('passwordbaru123', $student->password));
        $this->assertNotSame($oldHash, $student->password);
    }

    public function test_password_is_kept_when_left_empty(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $student = User::factory()->create();
        $oldHash = $student->password;

        $this->actingAs($superAdmin)->patch(route('admin.users.update', $student), [
            'name' => 'Ganti Nama Saja',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect();

        $student->refresh();
        $this->assertSame('Ganti Nama Saja', $student->name);
        $this->assertSame($oldHash, $student->password);
    }

    public function test_admin_cannot_change_account_data(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->create();

        $this->actingAs($admin)->patch(route('admin.users.update', $student), [
            'name' => 'Nama Baru',
        ])->assertForbidden();

        $this->assertNotSame('Nama Baru', $student->fresh()->name);
    }

    public function test_super_admin_can_delete_a_user(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $student = User::factory()->create();

        $this->actingAs($superAdmin)->delete(route('admin.users.destroy', $student))->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $student->id]);
    }
}
