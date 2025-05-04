<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class GlobalErrorHandler
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (Throwable $exception) {
            Log::error('Global error handler caught exception: ' . $exception->getMessage(), [
                'exception' => $exception,
                'trace' => $exception->getTraceAsString(),
                'request' => [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
            ]);

            return response()->json([
                'message' => 'An unexpected error occurred',
                'errors' => ['general' => ['The server encountered an unexpected error']],
                'code' => 'SERVER_ERROR'
            ], 500);
        }
    }
}
