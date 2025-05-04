<?php

namespace App\UnitOfWork;

use Closure;

interface UnitOfWorkInterface
{
    /**
     * Execute a function within a transaction.
     *
     * @param Closure $callback
     * @return mixed
     */
    public function execute(Closure $callback);
}
