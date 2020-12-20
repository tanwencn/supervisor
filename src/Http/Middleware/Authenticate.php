<?php

namespace Tanwencn\Supervisor\Http\Middleware;


use Tanwencn\Supervisor\Supervisor;

class Authenticate
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|null
     */
    public function handle($request, $next)
    {
        return Supervisor::check($request) ? $next($request) : abort(403);
    }
}
