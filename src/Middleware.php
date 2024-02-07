<?php

namespace Yomafleet\FeatureFlag;

use Closure;
use Yomafleet\FeatureFlag\Facade;

class Middleware
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $name
     * @return mixed
     */
    public function handle($request, Closure $next, string $name)
    {
        abort_if(!Facade::enabled($name), 403);

        return $next($request);
    }
}
