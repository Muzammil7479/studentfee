<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsStandardUser
{
    /**
     * Allow the request through for the "user" role.
     *
     * Admins are also allowed through, since admins have full access to
     * everything a standard user can see.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->isUser() && ! $user->isAdmin()) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
