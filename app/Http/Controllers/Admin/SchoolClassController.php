<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    public function index(): View
    {
        return view('admin.classes.index', [
            'classes' => SchoolClass::ordered()->withCount(['users', 'submissions'])->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'grade' => ['required', Rule::in(['X', 'XI', 'XII'])],
            'name' => ['required', 'string', 'max:50', Rule::unique('school_classes', 'name')],
        ]);

        SchoolClass::create([
            ...$validated,
            'sort_order' => (SchoolClass::max('sort_order') ?? 0) + 1,
            'is_active' => true,
        ]);

        return back()->with('status', 'Kelas '.$validated['name'].' ditambahkan.');
    }

    public function update(Request $request, SchoolClass $schoolClass): RedirectResponse
    {
        $validated = $request->validate([
            'grade' => ['required', Rule::in(['X', 'XI', 'XII'])],
            'name' => ['required', 'string', 'max:50', Rule::unique('school_classes', 'name')->ignore($schoolClass)],
            'is_active' => ['boolean'],
        ]);

        $schoolClass->update([...$validated, 'is_active' => $request->boolean('is_active')]);

        return back()->with('status', 'Kelas '.$schoolClass->name.' diperbarui.');
    }

    public function destroy(SchoolClass $schoolClass): RedirectResponse
    {
        if ($schoolClass->submissions()->exists()) {
            $schoolClass->update(['is_active' => false]);

            return back()->with('status', 'Kelas '.$schoolClass->name.' sudah memiliki data, jadi dinonaktifkan (tidak dihapus).');
        }

        $schoolClass->delete();

        return back()->with('status', 'Kelas '.$schoolClass->name.' dihapus.');
    }
}
