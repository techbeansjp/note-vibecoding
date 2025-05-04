<?php

namespace App\BusinessLogic\Services;

use Closure;
use Exception;
use Illuminate\Support\Facades\Log;

class RetryService
{
    /**
     * Execute a function with retry logic.
     *
     * @param Closure $operation
     * @param int $maxAttempts
     * @param int $initialDelay
     * @param float $backoffFactor
     * @param array $retryableExceptions
     * @return mixed
     * @throws Exception
     */
    public function execute(
        Closure $operation,
        int $maxAttempts = 3,
        int $initialDelay = 100,
        float $backoffFactor = 2.0,
        array $retryableExceptions = [Exception::class]
    ) {
        $attempts = 0;
        $delay = $initialDelay;

        while (true) {
            $attempts++;

            try {
                return $operation();
            } catch (Exception $e) {
                $shouldRetry = false;
                foreach ($retryableExceptions as $exceptionClass) {
                    if ($e instanceof $exceptionClass) {
                        $shouldRetry = true;
                        break;
                    }
                }

                if (!$shouldRetry) {
                    throw $e;
                }

                if ($attempts >= $maxAttempts) {
                    Log::warning("Retry failed after {$attempts} attempts: " . $e->getMessage());
                    throw $e;
                }

                Log::info("Retrying operation after exception: " . $e->getMessage() . " (Attempt {$attempts} of {$maxAttempts})");

                usleep($delay * 1000);
                $delay *= $backoffFactor;
            }
        }
    }
}
