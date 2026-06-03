<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->role !== 'admin') {
            // Redirect cashiers to their own dashboard instead of a raw 403.
            if ($request->user()->role === 'cashier') {
                return redirect()->route('cashier.dashboard')
                    ->with('warning', 'You do not have admin access.');
            }

            abort(403, 'Admin access only.');
        }

        return $next($request);
    }
}