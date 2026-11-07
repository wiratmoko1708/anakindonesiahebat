<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\SchoolClass;
use App\Models\Submission;
use App\Models\User;
use Database\Seeders\QuestionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(QuestionSeeder::class);
    }

    public function test_user_without_class_must_choose_one_first(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('submissions.create'))->assertRedirect(route('class.edit'));

        $schoolClass = SchoolClass::factory()->create();
        $this->actingAs($user)->put(route('class.update'), ['school_class_id' => $schoolClass->id])
            ->assertRedirect(route('dashboard'));

        $this->assertSame($schoolClass->id, $user->fresh()->school_class_id);
    }

    public function test_submission_stores_answers_and_average(): void
    {
        $user = User::factory()->withClass()->create();

        $this->actingAs($user)->post(route('submissions.store'), $this->payload(4))
            ->assertRedirect(route('submissions.show', 1));

        $submission = Submission::sole();
        $this->assertSame(1, $submission->attempt_number);
        $this->assertSame(4.0, $submission->average_score);
        $this->assertSame($user->school_class_id, $submission->school_class_id);
        $this->assertSame(Question::count(), $submission->answers()->count());
    }

    public function test_incomplete_answers_are_rejected(): void
    {
        $user = User::factory()->withClass()->create();
        $payload = $this->payload(3);
        array_pop($payload['answers']);

        $this->actingAs($user)->post(route('submissions.store'), $payload)->assertSessionHasErrors();
        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_user_can_submit_at_most_seven_times(): void
    {
        $user = User::factory()->withClass()->create();

        for ($attempt = 1; $attempt <= 7; $attempt++) {
            $this->actingAs($user)->post(route('submissions.store'), $this->payload(2))->assertSessionHasNoErrors();
        }

        $this->actingAs($user)->post(route('submissions.store'), $this->payload(2))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->assertSame(7, $user->submissions()->count());
        $this->assertSame(range(1, 7), $user->submissions()->orderBy('attempt_number')->pluck('attempt_number')->all());
        $this->actingAs($user)->get(route('submissions.create'))->assertRedirect(route('dashboard'));
    }

    public function test_student_can_print_own_result_but_not_others(): void
    {
        $owner = User::factory()->withClass()->create();
        $other = User::factory()->withClass()->create();
        $this->actingAs($owner)->post(route('submissions.store'), $this->payload(3));
        $submission = Submission::sole();

        $this->actingAs($owner)->get(route('submissions.pdf', $submission))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->actingAs($owner)->get(route('submissions.my-report'))->assertOk();

        $this->actingAs($other)->get(route('submissions.pdf', $submission))->assertForbidden();
        $this->actingAs($other)->get(route('submissions.show', $submission))->assertForbidden();
    }

    /**
     * @return array{answers: array<int, int>, reflection: string}
     */
    private function payload(int $score): array
    {
        return [
            'answers' => Question::pluck('id')->mapWithKeys(fn (int $id): array => [$id => $score])->all(),
            'reflection' => 'Saya ingin tidur lebih cepat.',
        ];
    }
}
