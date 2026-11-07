<?php

namespace App\Http\Controllers;

use App\Actions\RecordSubmission;
use App\Exceptions\SubmissionLimitReachedException;
use App\Http\Requests\StoreSubmissionRequest;
use App\Models\Question;
use App\Models\Submission;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class SubmissionController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->hasReachedSubmissionLimit()) {
            return redirect()->route('dashboard')
                ->with('error', 'Kamu sudah mengisi kuesioner sebanyak '.config('kaih.max_submissions').' kali. Terima kasih atas partisipasimu! 🎉');
        }

        return view('submissions.create', [
            'user' => $user->load('schoolClass'),
            'questionsByHabit' => Question::active()->ordered()->get()->groupBy(fn (Question $question): string => $question->habit->value),
            'attemptNumber' => $user->submissions()->count() + 1,
            'maxSubmissions' => config('kaih.max_submissions'),
            'scale' => config('kaih.scale'),
        ]);
    }

    public function store(StoreSubmissionRequest $request, RecordSubmission $recordSubmission): RedirectResponse
    {
        try {
            $submission = $recordSubmission->handle(
                $request->user(),
                $request->activeQuestions(),
                $request->validated('answers'),
                $request->validated('reflection'),
            );
        } catch (SubmissionLimitReachedException) {
            return redirect()->route('dashboard')
                ->with('error', 'Batas '.config('kaih.max_submissions').' kali pengisian sudah tercapai.');
        }

        return redirect()->route('submissions.show', $submission)
            ->with('celebrate', true)
            ->with('status', 'Terima kasih! Pengisian ke-'.$submission->attempt_number.' berhasil disimpan. 🎉');
    }

    public function show(Submission $submission): View
    {
        Gate::authorize('view', $submission);

        $submission->load(['user', 'schoolClass', 'answers']);

        return view('submissions.show', [
            'submission' => $submission,
            'habitScores' => $submission->habitScores(),
            'scale' => config('kaih.scale'),
        ]);
    }

    public function pdf(Submission $submission): Response
    {
        Gate::authorize('view', $submission);

        $submission->load(['user', 'schoolClass', 'answers']);

        return Pdf::loadView('pdf.submission', [
            'submission' => $submission,
            'habitScores' => $submission->habitScores(),
            'scale' => config('kaih.scale'),
        ])->setPaper('a4')->stream($this->pdfFilename($submission));
    }

    public function myReport(Request $request): Response
    {
        $user = $request->user()->load('schoolClass');
        $submissions = $user->submissions()->with('answers')->orderBy('attempt_number')->get();

        return Pdf::loadView('pdf.student-report', [
            'user' => $user,
            'submissions' => $submissions,
            'maxSubmissions' => config('kaih.max_submissions'),
        ])->setPaper('a4')->stream('rekap-7kaih-'.str($user->name)->slug().'.pdf');
    }

    private function pdfFilename(Submission $submission): string
    {
        return sprintf('7kaih-%s-pengisian-%d.pdf', str($submission->user->name)->slug(), $submission->attempt_number);
    }
}
