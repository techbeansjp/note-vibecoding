<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter as RateLimiterFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter as RateLimiterFacadeAlias;
use Symfony\Component\HttpFoundation\Response;

class RateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $limiter = 'api'): Response
    {
        $limiter = RateLimiterFacadeAlias::limiter($limiter);

        $key = $request->user()?->id ?: $request->ip();

        if (RateLimiterFacadeAlias::tooManyAttempts($key, 1000)) {
            return response()->json([
                'message' => 'Too many requests',
                'errors' => ['rate_limit' => ['Rate limit exceeded. Please try again later.']],
                'code' => 'RATE_LIMIT_EXCEEDED'
            ], 429);
        }

        RateLimiterFacadeAlias::hit($key);

        $response = $next($request);
        $response->headers->add([
            'X-RateLimit-Limit' => 1000,
            'X-RateLimit-Remaining' => RateLimiterFacadeAlias::remaining($key, 1000),
        ]);

        return $response;
    }
}
