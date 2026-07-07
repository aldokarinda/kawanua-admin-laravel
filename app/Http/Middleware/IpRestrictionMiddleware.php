<?php

namespace App\Http\Middleware;

use App\Services\IpRestrictionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IpRestrictionMiddleware
{
    protected IpRestrictionService $ipRestrictionService;

    public function __construct(IpRestrictionService $ipRestrictionService)
    {
        $this->ipRestrictionService = $ipRestrictionService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->ipRestrictionService->isAllowed($request->ip())) {
            abort(403, 'Access denied. Your IP address (' . $request->ip() . ') is restricted.');
        }

        return $next($request);
    }
}
