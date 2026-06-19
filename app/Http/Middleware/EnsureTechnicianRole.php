<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTechnicianRole
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            abort(403, 'Access denied. You must be logged in.');
        }

        $user = auth()->user();

        // Check if user has technician role or has a technician profile
        if ($user->role !== 'technician' && !$user->technicians()->exists()) {
            abort(403, 'Access denied. You do not have technician privileges.');
        }

        return $next($request);
    }
}
