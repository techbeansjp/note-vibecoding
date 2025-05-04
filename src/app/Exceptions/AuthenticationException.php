<?php

namespace App\Exceptions;

use Exception;

class AuthenticationException extends Exception
{
    /**
     * @var array
     */
    protected $errors;

    /**
     * @var string
     */
    protected $errorCode;

    /**
     * Create a new authentication exception instance.
     *
     * @param string $message
     * @param array $errors
     * @param string $errorCode
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $message = 'Authentication failed',
        array $errors = [],
        string $errorCode = 'AUTH_ERROR',
        int $code = 401,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
        $this->errorCode = $errorCode;
    }

    /**
     * Get the errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get the error code.
     *
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}
