<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Allow the request only when the user has one of the given roles.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $userRole = $request->user()?->role;

        abort_unless(
            $userRole instanceof Role && in_array($userRole->value, $roles, true),
            403,
            'Kamu tidak memiliki akses ke halaman ini.',
        );

        return $next($request);
    }
}
