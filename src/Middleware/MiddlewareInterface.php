<?php
declare(strict_types=1);

namespace Ctefan\Redux\Middleware;

interface MiddlewareInterface
{
    /**
     * @param callable $getState
     * @param callable $dispatch
     * @return callable
     */
    public function __invoke(callable $getState, callable $dispatch): callable;
}