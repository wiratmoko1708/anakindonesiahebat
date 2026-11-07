<?php

namespace App\Services;

use App\Enums\Habit;
use App\Models\Submission;
use App\Models\SubmissionAnswer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Aggregates 7 KAIH answers for dashboards and PDF reports.
 */
class KaihStatistics
{
    /**
     * Average score per habit (1–4) for the filtered submissions.
     *
     * @param  array{school_class_id?: int|null, grade?: string|null, user_id?: int|null}  $filters
     * @return Collection<string, array{habit: Habit, average: float|null, responses: int}>
     */
    public function habitAverages(array $filters = []): Collection
    {
        $rows = SubmissionAnswer::query()
            ->whereIn('submission_id', $this->submissionQuery($filters)->select('id'))
            ->selectRaw('habit, AVG(score) as average, COUNT(DISTINCT submission_id) as responses')
            ->groupBy('habit')
            ->get()
            ->keyBy(fn (SubmissionAnswer $row): string => $row->habit->value);

        return collect(Habit::cases())->mapWithKeys(fn (Habit $habit): array => [$habit->value => [
            'habit' => $habit,
            'average' => isset($rows[$habit->value]) ? round((float) $rows[$habit->value]->average, 2) : null,
            'responses' => (int) ($rows[$habit->value]->responses ?? 0),
        ]]);
    }

    /**
     * Average overall score per attempt number (1..max), to show the trend across fillings.
     *
     * @param  array{school_class_id?: int|null, grade?: string|null, user_id?: int|null}  $filters
     * @return Collection<int, array{attempt: int, average: float|null, responses: int}>
     */
    public function attemptTrend(array $filters = []): Collection
    {
        $rows = $this->submissionQuery($filters)
            ->selectRaw('attempt_number, AVG(average_score) as average, COUNT(*) as responses')
            ->groupBy('attempt_number')
            ->get()
            ->keyBy('attempt_number');

        return collect(range(1, config('kaih.max_submissions')))->map(fn (int $attempt): array => [
            'attempt' => $attempt,
            'average' => isset($rows[$attempt]) ? round((float) $rows[$attempt]->average, 2) : null,
            'responses' => (int) ($rows[$attempt]->responses ?? 0),
        ]);
    }

    /**
     * Distribution of submissions over the score levels.
     *
     * @param  array{school_class_id?: int|null, grade?: string|null, user_id?: int|null}  $filters
     * @return Collection<int, array{label: string, emoji: string, color: string, classes: string, count: int}>
     */
    public function levelDistribution(array $filters = []): Collection
    {
        $scores = $this->submissionQuery($filters)->pluck('average_score');

        return collect(config('kaih.levels'))->map(fn (array $level): array => [
            ...$level,
            'count' => $scores->filter(fn (float $score): bool => Submission::levelFor($score)['label'] === $level['label'])->count(),
        ]);
    }

    /**
     * @param  array{school_class_id?: int|null, grade?: string|null, user_id?: int|null}  $filters
     * @return array{submissions: int, respondents: int, average: float|null}
     */
    public function summary(array $filters = []): array
    {
        $query = $this->submissionQuery($filters);

        return [
            'submissions' => (clone $query)->count(),
            'respondents' => (clone $query)->distinct()->count('user_id'),
            'average' => ($average = (clone $query)->avg('average_score')) !== null ? round((float) $average, 2) : null,
        ];
    }

    /**
     * @param  array{school_class_id?: int|null, grade?: string|null, user_id?: int|null}  $filters
     * @return Builder<Submission>
     */
    public function submissionQuery(array $filters = []): Builder
    {
        return Submission::query()
            ->when($filters['school_class_id'] ?? null, fn (Builder $query, int $classId) => $query->where('school_class_id', $classId))
            ->when($filters['grade'] ?? null, fn (Builder $query, string $grade) => $query->whereHas('schoolClass', fn (Builder $classQuery) => $classQuery->where('grade', $grade)))
            ->when($filters['user_id'] ?? null, fn (Builder $query, int $userId) => $query->where('user_id', $userId));
    }
}
