<?php

namespace App\Models;

use App\Enums\Habit;
use Database\Factories\SubmissionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

#[Fillable(['user_id', 'school_class_id', 'attempt_number', 'average_score', 'reflection', 'submitted_at'])]
class Submission extends Model
{
    /** @use HasFactory<SubmissionFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'average_score' => 'float',
            'submitted_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<SchoolClass, $this>
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * @return HasMany<SubmissionAnswer, $this>
     */
    public function answers(): HasMany
    {
        return $this->hasMany(SubmissionAnswer::class);
    }

    /**
     * Average score per habit, in the canonical 7 KAIH order.
     *
     * @return Collection<string, array{habit: Habit, average: float, answers: Collection<int, SubmissionAnswer>}>
     */
    public function habitScores(): Collection
    {
        $answersByHabit = $this->answers->groupBy(fn (SubmissionAnswer $answer): string => $answer->habit->value);

        return collect(Habit::cases())
            ->filter(fn (Habit $habit): bool => $answersByHabit->has($habit->value))
            ->mapWithKeys(fn (Habit $habit): array => [$habit->value => [
                'habit' => $habit,
                'average' => round((float) $answersByHabit[$habit->value]->avg('score'), 2),
                'answers' => $answersByHabit[$habit->value],
            ]]);
    }

    /**
     * Resolve the descriptive level for an average score.
     *
     * @return array{min: float, label: string, emoji: string, color: string, classes: string}
     */
    public static function levelFor(float $average): array
    {
        foreach (config('kaih.levels') as $level) {
            if ($average >= $level['min']) {
                return $level;
            }
        }

        return last(config('kaih.levels'));
    }

    /**
     * @return array{min: float, label: string, emoji: string, color: string, classes: string}
     */
    public function level(): array
    {
        return self::levelFor($this->average_score);
    }

    public function scorePercentage(): int
    {
        return (int) round(($this->average_score / 4) * 100);
    }
}
