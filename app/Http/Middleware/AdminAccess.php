<?php

namespace App\Http\Middleware;

use App\Exceptions\Unauthorized;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class AdminAccess
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
        if (!$request->user() || !in_array($request->user()->level, [User::LEVEL_8, User::LEVEL_MASTER])) {
            throw new Unauthorized();
        }

        return $next($request);
    }
}
