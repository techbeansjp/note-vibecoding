<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return $this->handleApiException($e, $request);
            }
        });
    }

    /**
     * Handle API exceptions.
     *
     * @param Throwable $exception
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    private function handleApiException(Throwable $exception, $request): JsonResponse
    {
        if ($exception instanceof ValidationException) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $exception->errors(),
                'code' => 'VALIDATION_ERROR'
            ], 422);
        }

        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
                'code' => $exception->getErrorCode()
            ], $exception->getCode());
        }

        $statusCode = method_exists($exception, 'getStatusCode')
            ? $exception->getStatusCode()
            : 500;

        $message = $exception->getMessage();
        if (empty($message) || $statusCode === 500) {
            $message = 'An unexpected error occurred';
        }

        return response()->json([
            'message' => $message,
            'errors' => ['general' => ['The server encountered an unexpected error']],
            'code' => 'SERVER_ERROR'
        ], $statusCode);
    }
}
