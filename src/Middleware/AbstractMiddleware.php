<?php
declare(strict_types=1);

namespace Ctefan\Redux\Middleware;

use Ctefan\Redux\Action\ActionInterface;

abstract class AbstractMiddleware implements MiddlewareInterface
{
    private $getState;

    public function __invoke(callable $getState): callable
    {
        $this->getState = $getState;

        return function(callable $next) {
            return function(ActionInterface $action) use ($next) {
                return $this->dispatch($action, $next);
            };
        };
    }

    protected function getState()
    {
        return call_user_func($this->getState);
    }

    abstract protected function dispatch(ActionInterface $action, callable $next): ActionInterface;
}