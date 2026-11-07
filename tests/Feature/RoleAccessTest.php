<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_sent_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_student_cannot_open_admin_pages(): void
    {
        $student = User::factory()->withClass()->create();

        $this->actingAs($student)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($student)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_admin_and_super_admin_can_open_admin_pages_and_print_report(): void
    {
        Submission::factory()->create();

        foreach ([User::factory()->admin()->create(), User::factory()->superAdmin()->create()] as $manager) {
            foreach (['admin.dashboard', 'admin.submissions.index', 'admin.users.index', 'admin.classes.index', 'admin.questions.index'] as $routeName) {
                $this->actingAs($manager)->get(route($routeName))->assertOk();
            }

            $this->actingAs($manager)->get(route('admin.submissions.report'))
                ->assertOk()
                ->assertHeader('content-type', 'application/pdf');
        }
    }

    public function test_only_super_admin_can_change_roles(): void
    {
        $admin = User::factory()->admin()->create();
        $superAdmin = User::factory()->superAdmin()->create();
        $student = User::factory()->create();

        $this->actingAs($admin)->patch(route('admin.users.update', $student), ['role' => Role::Admin->value])->assertForbidden();
        $this->assertSame(Role::User, $student->fresh()->role);

        $this->actingAs($superAdmin)->patch(route('admin.users.update', $student), ['role' => Role::Admin->value])->assertRedirect();
        $this->assertSame(Role::Admin, $student->fresh()->role);
    }

    public function test_super_admin_cannot_demote_themselves(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->patch(route('admin.users.update', $superAdmin), ['role' => Role::User->value])
            ->assertSessionHas('error');

        $this->assertSame(Role::SuperAdmin, $superAdmin->fresh()->role);
    }

    public function test_deleting_a_submission_restores_quota_and_renumbers(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $student = User::factory()->withClass()->create();
        $submissions = collect(range(1, 3))->map(fn (int $attempt) => Submission::factory()->for($student)->create(['attempt_number' => $attempt]));

        $this->actingAs($superAdmin)->delete(route('admin.submissions.destroy', $submissions[0]))->assertRedirect();

        $this->assertSame([1, 2], $student->submissions()->orderBy('attempt_number')->pluck('attempt_number')->all());
        $this->assertSame(5, $student->fresh()->remainingSubmissions());
    }

    public function test_admin_cannot_delete_submissions(): void
    {
        $submission = Submission::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('admin.submissions.destroy', $submission))
            ->assertForbidden();
    }
}
