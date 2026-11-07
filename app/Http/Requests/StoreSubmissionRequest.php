<?php

namespace App\Http\Requests;

use App\Models\Question;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

class StoreSubmissionRequest extends FormRequest
{
    /** @var Collection<int, Question>|null */
    private ?Collection $activeQuestions = null;

    public function authorize(): bool
    {
        return $this->user()?->hasSchoolClass() ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'answers' => ['required', 'array'],
            'reflection' => ['nullable', 'string', 'max:500'],
        ];

        foreach ($this->activeQuestions() as $question) {
            $rules["answers.{$question->id}"] = ['required', 'integer', 'between:1,'.count(config('kaih.scale'))];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'answers.required' => 'Jawab semua pertanyaan terlebih dahulu.',
            'answers.*.required' => 'Pertanyaan ini belum dijawab.',
            'answers.*.between' => 'Pilihan jawaban tidak valid.',
            'reflection.max' => 'Refleksi maksimal 500 karakter.',
        ];
    }

    /**
     * @return Collection<int, Question>
     */
    public function activeQuestions(): Collection
    {
        return $this->activeQuestions ??= Question::active()->ordered()->get();
    }
}
