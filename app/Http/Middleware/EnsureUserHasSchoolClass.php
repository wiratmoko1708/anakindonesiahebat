<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasSchoolClass
{
    /**
     * Send users without a class to the class selection page first.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->hasSchoolClass()) {
            return redirect()->route('class.edit')
                ->with('status', 'Pilih kelasmu terlebih dahulu sebelum melanjutkan.');
        }

        return $next($request);
    }
}
