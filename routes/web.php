<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\DevLoginController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ClassSelectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if ($user = auth()->user()) {
        return redirect()->route($user->canManage() ? 'admin.dashboard' : 'dashboard');
    }

    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');
    Route::get('/auth/google/complete', [GoogleController::class, 'complete'])->name('google.complete');
    Route::post('/dev-login', DevLoginController::class)->name('dev-login');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');

    Route::get('/kelas', [ClassSelectionController::class, 'edit'])->name('class.edit');
    Route::put('/kelas', [ClassSelectionController::class, 'update'])->name('class.update');

    Route::middleware('class.selected')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::get('/kuesioner', [SubmissionController::class, 'create'])->name('submissions.create');
        Route::post('/kuesioner', [SubmissionController::class, 'store'])->name('submissions.store');
        Route::get('/rekap-saya.pdf', [SubmissionController::class, 'myReport'])->name('submissions.my-report');
    });

    Route::get('/hasil/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::get('/hasil/{submission}/pdf', [SubmissionController::class, 'pdf'])->name('submissions.pdf');

    Route::prefix('admin')->name('admin.')->middleware('role:super_admin,admin')->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');
        Route::get('/hasil', [Admin\SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/hasil/laporan.pdf', [Admin\SubmissionController::class, 'report'])->name('submissions.report');
        Route::delete('/hasil/{submission}', [Admin\SubmissionController::class, 'destroy'])->name('submissions.destroy');

        Route::get('/pengguna', [Admin\UserController::class, 'index'])->name('users.index');
        Route::post('/pengguna', [Admin\UserController::class, 'store'])->name('users.store');
        Route::get('/pengguna/{user}/edit', [Admin\UserController::class, 'edit'])->name('users.edit');
        Route::patch('/pengguna/{user}', [Admin\UserController::class, 'update'])->name('users.update');
        Route::delete('/pengguna/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');

        Route::resource('kelas', Admin\SchoolClassController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['kelas' => 'schoolClass'])
            ->names('classes');

        Route::resource('pertanyaan', Admin\QuestionController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['pertanyaan' => 'question'])
            ->names('questions');
    });
});
