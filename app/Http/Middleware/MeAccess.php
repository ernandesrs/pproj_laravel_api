<?php

namespace App\Http\Middleware;

use App\Exceptions\NotAuthenticated;
use Closure;
use Illuminate\Http\Request;

class MeAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user())
            throw new NotAuthenticated();

        return $next($request);
    }
}
