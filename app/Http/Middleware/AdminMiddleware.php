<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (\Auth::user())
        {
            if (\Auth::user()->role == 1)
            {
                return $next($request);
            }else if (\Auth::user()->role == 2)
            {
                return redirect()->guest('/kasubag');
            }else if (\Auth::user()->role == 3)
            {
                return redirect()->guest('/teknisi');
            }else if (\Auth::user()->role == 0)
            {
                return redirect()->guest('/user');
            }
        }else
        {
            return redirect()->guest('/');
        }
    }
}
