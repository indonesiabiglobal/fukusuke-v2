<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AllowIframe
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Allow iframe from Cordova app
        $response->headers->remove('X-Frame-Options');
        $response->headers->set('Content-Security-Policy', "frame-ancestors *");

        return $response;
    }
}
