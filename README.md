# redux-php

[![Build Status](https://travis-ci.com/Cmytxy/redux-php.svg?branch=master)](https://travis-ci.com/Cmytxy/redux-php)

A PHP port of [Redux](https://github.com/reduxjs/redux/blob/master/src/applyMiddleware.js).
Partly inspired by the ports of [RafaelFontes](https://github.com/RafaelFontes/php-mars)
and [rikbruil](https://github.com/rikbruil/php-redux).

## Background

This library was created to be used in a framework for interactive cli applications.

## Example usage

### Reducers

```php
// Create a reducer.
$reducer = function($state, ActionInterface $action) {
    switch ($action->getType()) {
        case 'add':
            $state += $action->getPayload();
            break;
        case 'subtract':
            $state -= $action->getPayload();
            break;
    }
    return $state;
};
$initialState = 0;

// Create the store.
$store = Redux::createStore($reducer, $initialState);

// Dispatch some actions.
$store->dispatch(new Action('add', 5));
$store->dispatch(new Action('subtract', 2));

assert(3 === $store->getState());
```

### Middleware

Middleware are just callables. If you don't want to use closures, you can extend 
`Ctefan\Redux\Middleware\AbstractMiddleware` and implement method `handleAction(ActionInterface $action, callable $next)`.

```php
// Create the middleware.
$middleware = function(callable $getState, callable $dispatch): callable {
    return function($next): callable {
        return function(ActionInterface $action) use ($next) {
            // TODO do something like fetching data from a remote server, waiting for a promise etc.
            return $next($action);
        }
    }
};

// Create the store.
$store = Redux::createStore($reducer, $initialState, Redux::applyMiddleware($middleware));
```