<?php

namespace Railroad\LeadTracker\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;

class LeadTrackerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}