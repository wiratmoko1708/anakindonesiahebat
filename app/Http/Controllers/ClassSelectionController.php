<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSchoolClassSelectionRequest;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassSelectionController extends Controller
{
    public function edit(Request $request): View
    {
        return view('class.edit', [
            'user' => $request->user(),
            'classesByGrade' => SchoolClass::active()->ordered()->get()->groupBy('grade'),
        ]);
    }

    public function update(UpdateSchoolClassSelectionRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->update(['school_class_id' => $request->integer('school_class_id')]);

        return redirect()->intended($user->canManage() ? route('admin.dashboard') : route('dashboard'))
            ->with('status', 'Kelas '.$user->schoolClass->name.' tersimpan. Ayo mulai isi kuesioner! 🚀');
    }
}
