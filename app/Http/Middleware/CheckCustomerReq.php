<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCustomerReq
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
        $token = $request->bearerToken();
        if(!$token) {
            return response()->json(['success' => false, 'msg' => 'Unauthorized'], 401);
        } else {
            if($token != 'd70e61a9-9c71-40ee-b62b-f5df85be11d7') {
                return response()->json(['success' => false, 'msg' => 'Invalid Token'], 401);
            }
        }
        
        return $next($request);
    }
}
