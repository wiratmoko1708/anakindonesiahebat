<?php

namespace App\Http\Controllers;

use App\Services\KaihStatistics;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, KaihStatistics $statistics): View
    {
        $user = $request->user()->load('schoolClass');
        $filters = ['user_id' => $user->id];

        return view('dashboard', [
            'user' => $user,
            'submissions' => $user->submissions()->latest('submitted_at')->get(),
            'maxSubmissions' => config('kaih.max_submissions'),
            'remaining' => $user->remainingSubmissions(),
            'habitAverages' => $statistics->habitAverages($filters),
            'trend' => $statistics->attemptTrend($filters),
            'summary' => $statistics->summary($filters),
        ]);
    }
}
