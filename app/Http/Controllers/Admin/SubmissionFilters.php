<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

final class SubmissionFilters
{
    /**
     * @return array{school_class_id: int|null, grade: string|null, search: string|null, attempt: int|null}
     */
    public static function fromRequest(Request $request): array
    {
        return [
            'school_class_id' => $request->integer('school_class_id') ?: null,
            'grade' => in_array($request->query('grade'), ['X', 'XI', 'XII'], true) ? $request->query('grade') : null,
            'search' => $request->filled('search') ? trim((string) $request->query('search')) : null,
            'attempt' => $request->integer('attempt') ?: null,
        ];
    }
}
