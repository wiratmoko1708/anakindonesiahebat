<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    public function view(User $user, Submission $submission): bool
    {
        return $user->canManage() || $submission->user_id === $user->id;
    }

    public function delete(User $user, Submission $submission): bool
    {
        return $user->isSuperAdmin();
    }
}
