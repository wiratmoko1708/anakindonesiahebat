<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Submission;
use App\Models\User;
use App\Services\KaihStatistics;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class SubmissionController extends Controller
{
    public function index(Request $request): View
    {
        $filters = SubmissionFilters::fromRequest($request);

        return view('admin.submissions.index', [
            'filters' => $filters,
            'classes' => SchoolClass::ordered()->get(),
            'submissions' => $this->filteredQuery($filters)
                ->with(['user', 'schoolClass'])
                ->latest('submitted_at')
                ->paginate(20)
                ->withQueryString(),
            'maxSubmissions' => config('kaih.max_submissions'),
        ]);
    }

    public function report(Request $request, KaihStatistics $statistics): Response
    {
        $filters = SubmissionFilters::fromRequest($request);
        $selectedClass = $filters['school_class_id'] ? SchoolClass::find($filters['school_class_id']) : null;

        $submissions = $this->filteredQuery($filters)
            ->with(['user', 'schoolClass', 'answers'])
            ->orderBy(SchoolClass::select('sort_order')->whereColumn('school_classes.id', 'submissions.school_class_id'))
            ->orderBy(User::select('name')->whereColumn('users.id', 'submissions.user_id'))
            ->orderBy('attempt_number')
            ->get();

        return Pdf::loadView('pdf.admin-report', [
            'filters' => $filters,
            'selectedClass' => $selectedClass,
            'submissions' => $submissions,
            'summary' => $statistics->summary($filters),
            'habitAverages' => $statistics->habitAverages($filters),
            'trend' => $statistics->attemptTrend($filters),
            'levels' => $statistics->levelDistribution($filters),
        ])->setPaper('a4', 'landscape')->stream('laporan-7kaih'.($selectedClass ? '-'.str($selectedClass->name)->slug() : '').'.pdf');
    }

    public function destroy(Submission $submission): RedirectResponse
    {
        Gate::authorize('delete', $submission);

        $owner = $submission->user;
        $submission->delete();

        $owner->submissions()->orderBy('attempt_number')->get()
            ->each(fn (Submission $remaining, int $index) => $remaining->update(['attempt_number' => $index + 1]));

        return back()->with('status', 'Data pengisian berhasil dihapus dan kuota siswa dikembalikan.');
    }

    /**
     * @param  array{school_class_id: int|null, grade: string|null, search: string|null, attempt: int|null}  $filters
     * @return Builder<Submission>
     */
    private function filteredQuery(array $filters): Builder
    {
        return app(KaihStatistics::class)->submissionQuery($filters)
            ->when($filters['attempt'], fn (Builder $query, int $attempt) => $query->where('attempt_number', $attempt))
            ->when($filters['search'], fn (Builder $query, string $search) => $query->whereHas(
                'user',
                fn (Builder $userQuery) => $userQuery->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"),
            ));
    }
}
