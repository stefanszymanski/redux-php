<?php
declare(strict_types=1);

namespace Ctefan\Redux;

use Ctefan\Redux\Action\ActionInterface;
use Ctefan\Redux\Exception\IsSettingUpMiddlewareException;
use Ctefan\Redux\Store\EnhanceableStoreInterface;
use Ctefan\Redux\Store\Store;

class Redux
{
    static public function createStore(callable $reducer, array $initialState = [], callable $enhancer = null): EnhanceableStoreInterface
    {
        if (null !== $enhancer) {
            $createStore = [self::class, 'create'];
            return $enhancer($createStore)($reducer, $initialState);
        }

        return new Store($reducer, $initialState);
    }

    static public function applyMiddleware(callable ...$middlewares): callable
    {
        return function(callable $createStore) use ($middlewares): callable {
            return function(callable $reducer, array $initialState = [], callable $enhancer = null) use ($middlewares, $createStore): EnhanceableStoreInterface {
                $store = $createStore($reducer, $initialState, $enhancer);

                $getStateFunction = [$store, 'getState'];
                $dispatchFunction = function(ActionInterface $action) {
                    throw new IsSettingUpMiddlewareException(
                        'Dispatching while constructing your middleware is not allowed. ' .
                        'Other middleware would not be applied to this dispatch.'
                    );
                };

                $chain = array_map(function($middleware) use ($getStateFunction, $dispatchFunction): callable {
                    return $middleware($getStateFunction, $dispatchFunction);
                }, $middlewares);

                $dispatchFunction = self::compose(...$chain)($store->getDispatcher());

                $store->setDispatcher($dispatchFunction);

                return $store;
            };
        };
    }

    static public function compose(callable ...$callables): callable
    {
        if (0 === count($callables)) {
            return function($argument) {
                return $argument;
            };
        }

        if (1 === count($callables)) {
            return $callables[0];
        }

        return array_reduce($callables, function(?callable $previous, callable $next): callable {
            if (null === $previous) {
                return $next;
            }
            return function(...$arguments) use ($previous, $next) {
                return $previous($next(...$arguments));
            };
        });
    }
}