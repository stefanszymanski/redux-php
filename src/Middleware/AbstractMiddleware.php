<?php
declare(strict_types=1);

namespace Ctefan\Redux\Middleware;

use Ctefan\Redux\Action\ActionInterface;

abstract class AbstractMiddleware implements MiddlewareInterface
{
    /**
     * @var callable
     */
    private $getStateFunction;

    /**
     * @var callable
     */
    private $dispatchFunction;

    /**
     * @param callable $getState
     * @param callable $dispatch
     * @return callable
     */
    public function __invoke(callable $getState, callable $dispatch): callable
    {
        $this->getStateFunction = $getState;
        $this->dispatchFunction = $dispatch;

        return function(callable $next) {
            return function(ActionInterface $action) use ($next) {
                return $this->handleAction($action, $next);
            };
        };
    }

    /**
     * @return mixed
     */
    protected function getState()
    {
        return call_user_func($this->getStateFunction);
    }

    /**
     * @param ActionInterface $action
     * @return ActionInterface
     */
    protected function dispatch(ActionInterface $action): ActionInterface
    {
        return call_user_func($this->dispatchFunction, $action);
    }

    /**
     * @param ActionInterface $action
     * @param callable $next
     * @return ActionInterface
     */
    abstract protected function handleAction(ActionInterface $action, callable $next): ActionInterface;
}