<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\KaihStatistics;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, KaihStatistics $statistics): View
    {
        $filters = SubmissionFilters::fromRequest($request);
        $maxSubmissions = config('kaih.max_submissions');

        $students = User::query()
            ->where('role', Role::User)
            ->when($filters['school_class_id'], fn ($query, int $classId) => $query->where('school_class_id', $classId))
            ->when($filters['grade'], fn ($query, string $grade) => $query->whereHas('schoolClass', fn ($classQuery) => $classQuery->where('grade', $grade)))
            ->withCount('submissions')
            ->get();

        $classAverages = SchoolClass::query()
            ->ordered()
            ->when($filters['grade'], fn ($query, string $grade) => $query->where('grade', $grade))
            ->when($filters['school_class_id'], fn ($query, int $classId) => $query->whereKey($classId))
            ->withCount('submissions')
            ->withAvg('submissions', 'average_score')
            ->get()
            ->filter(fn (SchoolClass $class): bool => $class->submissions_count > 0);

        return view('admin.dashboard', [
            'filters' => $filters,
            'classes' => SchoolClass::ordered()->get(),
            'grades' => SchoolClass::query()->distinct()->orderBy('grade')->pluck('grade'),
            'summary' => $statistics->summary($filters),
            'habitAverages' => $statistics->habitAverages($filters),
            'trend' => $statistics->attemptTrend($filters),
            'levels' => $statistics->levelDistribution($filters),
            'classAverages' => $classAverages,
            'studentCount' => $students->count(),
            'completedStudents' => $students->where('submissions_count', '>=', $maxSubmissions)->count(),
            'notStartedStudents' => $students->where('submissions_count', 0)->count(),
            'maxSubmissions' => $maxSubmissions,
        ]);
    }
}
