<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfCustomerAuthenticated
{
    /**
     * Send logged-in customers away from public/guest pages to the customer area.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard');
        }

        return $next($request);
    }
}
