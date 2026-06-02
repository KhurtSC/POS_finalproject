<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CashierMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->role !== 'cashier') {
            abort(403, 'Cashier access only.');
        }

        return $next($request);
    }
}
