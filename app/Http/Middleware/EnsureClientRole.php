<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureClientRole
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== 'client') {
            abort(403, 'Access denied.');
        }

        if (!auth()->user()->clientProfile) {
            abort(403, 'No client profile linked to this account.');
        }

        return $next($request);
    }
}
