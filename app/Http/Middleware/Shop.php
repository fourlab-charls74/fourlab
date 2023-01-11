<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Shop
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
        if($path != "/shop/login" && Auth::guard('head')->check() == false){
            return redirect('/shop/login');
        } else {
            return $next($request);
        }
    }
}
