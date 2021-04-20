<?php

namespace App\Http\Middleware;

use Closure;

class PayerAuth
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

        if (!empty(session('authenticatedPayer'))) {
            $request->session()->put('authenticatedPayer', time());
            return $next($request);
        }
    
        return redirect()->route('PayerLoginGet', ['lang' => 'en']);
    }
}
