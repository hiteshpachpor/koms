<?php

namespace App\Http\Middleware;

use Closure;

class AddAcceptJsonHeaderOnApi
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
        // Force add accept header in API calls
        if (substr($request->getPathInfo(), 0, 4) === "/api") {
            $request->headers->add(['accept' => 'application/json']);
        }

        return $next($request);
    }
}
