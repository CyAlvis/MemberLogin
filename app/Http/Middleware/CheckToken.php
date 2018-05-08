<?php

namespace App\Http\Middleware;

use Closure;
use Redis;

class CheckToken
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
        $token = $request->session()->get('_token');
        // $UT = json_decode(Redis::get($token),true)['UserType'];
        if(Redis::exists($token) ) {
            return $next($request);
        } else {
            return redirect('/');
        }
    }
}
