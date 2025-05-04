<?php

namespace App\UnitOfWork;

use Closure;
use Illuminate\Support\Facades\DB;

class DatabaseUnitOfWork implements UnitOfWorkInterface
{
    /**
     * Execute a function within a transaction.
     *
     * @param Closure $callback
     * @return mixed
     */
    public function execute(Closure $callback)
    {
        return DB::transaction(function () use ($callback) {
            return $callback();
        });
    }
}
