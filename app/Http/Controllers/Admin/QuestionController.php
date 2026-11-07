<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Habit;
use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public function index(): View
    {
        return view('admin.questions.index', [
            'questionsByHabit' => Question::ordered()->withCount('answers')->get()
                ->groupBy(fn (Question $question): string => $question->habit->value),
            'habits' => Habit::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'habit' => ['required', Rule::enum(Habit::class)],
            'body' => ['required', 'string', 'min:10', 'max:300'],
        ]);

        Question::create([
            ...$validated,
            'sort_order' => (Question::where('habit', $validated['habit'])->max('sort_order') ?? Question::max('sort_order') ?? 0) + 1,
            'is_active' => true,
        ]);

        return back()->with('status', 'Pertanyaan baru ditambahkan.');
    }

    public function update(Request $request, Question $question): RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'min:10', 'max:300'],
            'is_active' => ['boolean'],
        ]);

        $question->update([...$validated, 'is_active' => $request->boolean('is_active')]);

        return back()->with('status', 'Pertanyaan diperbarui.');
    }

    public function destroy(Question $question): RedirectResponse
    {
        if ($question->answers()->exists()) {
            $question->update(['is_active' => false]);

            return back()->with('status', 'Pertanyaan sudah memiliki jawaban, jadi dinonaktifkan agar data lama tetap utuh.');
        }

        $question->delete();

        return back()->with('status', 'Pertanyaan dihapus.');
    }
}
