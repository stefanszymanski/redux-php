<?php
declare(strict_types=1);

namespace test\Ctefan\Redux\Redux;

use Ctefan\Redux\Redux;
use Ctefan\Redux\Action\Action;
use Ctefan\Redux\Exception\IsSettingUpMiddlewareException;
use Ctefan\Redux\Action\ActionInterface;
use test\Ctefan\Redux\AbstractTestCase;

class ApplyMiddlewareTest extends AbstractTestCase
{
    public function testThrowWhenDispatchingDuringMiddlewareSetup(): void
    {
        $this->expectException(IsSettingUpMiddlewareException::class);

        $middleware = function(callable $getState, callable $dispatch) {
            $dispatch(new Action('test', 10));
        };
        Redux::applyMiddleware($middleware)([Redux::class, 'createStore'])($this->getTestReducer());
    }

    public function testWrapsDispatchMethodWithMiddlewareOnce(): void
    {
        $spy = $this->getMockCallable();
        $spy->expects($this->once())->method('__invoke');

        $middleware = function(callable $getState, callable $dispatch) use ($spy) {
            $spy();
            return function(callable $next) {
                return function(ActionInterface $action) use ($next) {
                    return $next($action);
                };
            };
        };

        $store = Redux::applyMiddleware($middleware)([Redux::class, 'createStore'])($this->getTestReducer());
    }

    public function testPassesRecursiveDispatchesThroughTheMiddlewareChain(): void
    {
        $spy = $this->getMockCallable();
        $spy->expects($this->exactly(2))->method('__invoke');

        $middleware = function(callable $getState, callable $dispatch) use ($spy) {
            return function(callable $next) use ($spy) {
                return function(ActionInterface $action) use ($next, $spy) {
                    $spy();
                    return $next($action);
                };
            };
        };

        $store = Redux::applyMiddleware($middleware)([Redux::class, 'createStore'])($this->getTestReducer());
        $store->dispatch(new Action('add', 1));
        $store->dispatch(new Action('add', 2));

        $this::assertEquals([1, 2,], $store->getState());
    }

    public function testWrapsDispatchMethodWithMiddlewareOnce2(): void
    {
        $spy = $this->getMockCallable();
        $spy->expects($this->exactly(3))->method('__invoke');

        $middleware = function(callable $getState, callable $dispatch) use ($spy) {
            $spy();
            return function(callable $next) {
                return function(ActionInterface $action) use ($next) {
                    return $next($action);
                };
            };
        };

        $store = Redux::applyMiddleware($middleware, $middleware, $middleware)([Redux::class, 'createStore'])($this->getTestReducer());
    }
}