<?php
declare(strict_types=1);

namespace Ctefan\Redux\Middleware;

interface MiddlewareInterface
{
    public function __invoke(callable $getState): callable;
}