<?php

namespace App\Actions;

use App\Exceptions\SubmissionLimitReachedException;
use App\Models\Question;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecordSubmission
{
    /**
     * Store one questionnaire attempt, enforcing the per-user limit.
     *
     * @param  Collection<int, Question>  $questions
     * @param  array<int|string, int|string>  $answers  score keyed by question id
     *
     * @throws SubmissionLimitReachedException
     */
    public function handle(User $user, Collection $questions, array $answers, ?string $reflection): Submission
    {
        return DB::transaction(function () use ($user, $questions, $answers, $reflection): Submission {
            User::whereKey($user->id)->lockForUpdate()->first();

            $completedAttempts = $user->submissions()->count();

            if ($completedAttempts >= config('kaih.max_submissions')) {
                throw new SubmissionLimitReachedException;
            }

            $scores = $questions->map(fn (Question $question): int => (int) $answers[$question->id]);

            $submission = $user->submissions()->create([
                'school_class_id' => $user->school_class_id,
                'attempt_number' => $completedAttempts + 1,
                'average_score' => round($scores->avg(), 2),
                'reflection' => filled($reflection) ? trim($reflection) : null,
                'submitted_at' => now(),
            ]);

            $submission->answers()->createMany($questions->map(fn (Question $question): array => [
                'question_id' => $question->id,
                'habit' => $question->habit,
                'question_text' => $question->body,
                'score' => (int) $answers[$question->id],
            ])->all());

            return $submission;
        });
    }
}
