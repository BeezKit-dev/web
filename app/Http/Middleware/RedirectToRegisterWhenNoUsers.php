<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToRegisterWhenNoUsers
{
    /**
     * Send visitors to registration when nobody has registered yet.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (User::registrationIsOpen()) {
            return redirect()->route('register');
        }

        return $next($request);
    }
}
