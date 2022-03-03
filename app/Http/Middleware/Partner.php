<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Partner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $path = $request->getPathInfo();
        if($path != "/partner/login" && Auth::guard('partner')->check() == false){
            return redirect('/partner/login');
        } else {
            return $next($request);
        }
    }
}
