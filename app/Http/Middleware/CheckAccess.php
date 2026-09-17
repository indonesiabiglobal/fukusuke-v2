<?php

namespace App\Http\Middleware;

use App\Services\AccessService;
use Closure;
use Illuminate\Http\Request;

class CheckAccess
{
    public function __construct(private AccessService $accessService)
    {
    }

    public function handle(Request $request, Closure $next, string ...$codes)
    {
        if (! $this->accessService->hasAny($request->user(), $codes)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
