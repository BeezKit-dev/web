<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegistrationIsOpen
{
    /**
     * Send visitors to the login page when registration is closed.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! User::registrationIsOpen()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
