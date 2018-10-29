<?php
declare(strict_types=1);

namespace Reducer;

class Transform
{
    static public function withInitialState($initialState): callable
    {
        return function(callable $reducer) use ($initialState) {
            return function ($state, $action) use ($initialState, $reducer) {
                if (null === $state) {
                    $state = $initialState;
                }
                return call_user_func($reducer, $state, $action);
            };
        };
    }
}