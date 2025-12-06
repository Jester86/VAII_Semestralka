<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in and is admin
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // Otherwise redirect or abort
        return redirect('/')->with('error', 'You do not have admin access.');
    }
}
