<?php

namespace App\BusinessLogic\Services;

use Closure;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CircuitBreaker
{
    /**
     * Circuit states
     */
    const CLOSED = 'closed';
    const OPEN = 'open';
    const HALF_OPEN = 'half_open';

    /**
     * The service name.
     *
     * @var string
     */
    private string $service;

    /**
     * The failure threshold before opening the circuit.
     *
     * @var int
     */
    private int $failureThreshold;

    /**
     * The time in seconds to wait before attempting to close the circuit.
     *
     * @var int
     */
    private int $resetTimeout;

    /**
     * CircuitBreaker constructor.
     *
     * @param string $service
     * @param int $failureThreshold
     * @param int $resetTimeout
     */
    public function __construct(string $service, int $failureThreshold = 5, int $resetTimeout = 30)
    {
        $this->service = $service;
        $this->failureThreshold = $failureThreshold;
        $this->resetTimeout = $resetTimeout;
    }

    /**
     * Execute a function with circuit breaker protection.
     *
     * @param Closure $operation
     * @param Closure|null $fallback
     * @return mixed
     * @throws Exception
     */
    public function execute(Closure $operation, Closure $fallback = null)
    {
        $state = $this->getState();

        if ($state === self::OPEN) {
            $lastFailure = Cache::get($this->getCacheKey('last_failure_time'));
            
            if (time() - $lastFailure >= $this->resetTimeout) {
                $this->setState(self::HALF_OPEN);
                $state = self::HALF_OPEN;
            } else {
                Log::warning("Circuit breaker for {$this->service} is open. Using fallback.");
                return $fallback ? $fallback() : $this->defaultFallback();
            }
        }

        try {
            $result = $operation();

            if ($state === self::HALF_OPEN) {
                $this->setState(self::CLOSED);
                $this->resetFailureCount();
            }

            return $result;
        } catch (Exception $e) {
            $this->recordFailure();
            
            if ($this->getFailureCount() >= $this->failureThreshold) {
                $this->setState(self::OPEN);
                Cache::put($this->getCacheKey('last_failure_time'), time(), $this->resetTimeout * 2);
                Log::error("Circuit breaker for {$this->service} opened due to {$this->failureThreshold} consecutive failures.");
            }

            if ($fallback) {
                return $fallback();
            }

            throw $e;
        }
    }

    /**
     * Get the current state of the circuit.
     *
     * @return string
     */
    private function getState(): string
    {
        return Cache::get($this->getCacheKey('state'), self::CLOSED);
    }

    /**
     * Set the state of the circuit.
     *
     * @param string $state
     * @return void
     */
    private function setState(string $state): void
    {
        Cache::put($this->getCacheKey('state'), $state, 3600);
    }

    /**
     * Record a failure.
     *
     * @return void
     */
    private function recordFailure(): void
    {
        $count = $this->getFailureCount() + 1;
        Cache::put($this->getCacheKey('failure_count'), $count, 3600);
    }

    /**
     * Get the current failure count.
     *
     * @return int
     */
    private function getFailureCount(): int
    {
        return Cache::get($this->getCacheKey('failure_count'), 0);
    }

    /**
     * Reset the failure count.
     *
     * @return void
     */
    private function resetFailureCount(): void
    {
        Cache::put($this->getCacheKey('failure_count'), 0, 3600);
    }

    /**
     * Get the cache key for the given suffix.
     *
     * @param string $suffix
     * @return string
     */
    private function getCacheKey(string $suffix): string
    {
        return "circuit_breaker:{$this->service}:{$suffix}";
    }

    /**
     * Default fallback function.
     *
     * @return mixed
     * @throws Exception
     */
    private function defaultFallback()
    {
        throw new Exception("Service {$this->service} is unavailable");
    }
}
