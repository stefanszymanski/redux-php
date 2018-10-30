<?php
declare(strict_types=1);

namespace Ctefan\Redux\Store;

use Ctefan\Redux\Action\Action;
use Ctefan\Redux\Action\ActionInterface;
use Ctefan\Redux\Exception\IsDispatchingException;
use Evenement\EventEmitter;
use Evenement\EventEmitterInterface;

class Store implements EnhanceableStoreInterface
{
    protected const EVENT_CHANGE = 'change';

    protected const ACTION_TYPE_INIT = '__initialize';

    /**
     * @var array
     */
    protected $state;

    /**
     * @var callable
     */
    protected $reducer;

    /**
     * @var callable
     */
    protected $dispatcher;

    /**
     * @var EventEmitterInterface
     */
    protected $emitter;

    /**
     * @var bool
     */
    protected $isDispatching;

    /**
     * Store constructor.
     *
     * @param callable $reducer
     * @param array $initialState
     */
    public function __construct(callable $reducer, array $initialState = [])
    {
        $this->reducer = $reducer;
        $this->state = $initialState;
        $this->emitter = new EventEmitter();

        $this->dispatcher = (function(ActionInterface $action): ActionInterface {
            return $this->_dispatch($action);
        })->bindTo($this);

        $this->initialize();
    }

    /**
     * Dispatch the given action.
     *
     * @param ActionInterface $action
     * @return ActionInterface
     */
    public function dispatch(ActionInterface $action): ActionInterface
    {
        return ($this->dispatcher)($action);
    }

    /**
     * Get the current state.
     *
     * @return mixed
     */
    public function getState(): array
    {
        return $this->state;
    }

    /**
     * Add a subscriber callback.
     *
     * @param callable $callback
     * @return callable
     * @throws IsDispatchingException
     */
    public function subscribe(callable $callback): callable
    {
        if (true === $this->isDispatching) {
            throw new IsDispatchingException();
        }

        $this->emitter->on(self::EVENT_CHANGE, $callback);
        $isSubscribed = true;

        // Return an unsubscribe callback.
        return (function() use (&$isSubscribed, $callback) {
            if (false === $isSubscribed) {
                return false;
            }
            $this->emitter->removeListener(self::EVENT_CHANGE, $callback);
            $isSubscribed = false;
            return true;
        })->bindTo($this);
    }

    /**
     * Set the reducer to use when dispatching.
     *
     * @param callable $reducer
     * @throws IsDispatchingException
     */
    public function setReducer(callable $reducer): void
    {
        if (true === $this->isDispatching) {
            throw new IsDispatchingException();
        }

        $this->reducer = $reducer;
        $this->initialize();
    }

    /**
     * Get the dispatcher.
     *
     * @return callable
     */
    public function getDispatcher(): callable
    {
        return $this->dispatcher;
    }

    /**
     * Set the dispatcher.
     *
     * @param callable $dispatcher
     */
    public function setDispatcher(callable $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

     /**
     * The base dispatcher method.
     *
     * @param ActionInterface $action
     * @return ActionInterface
     * @throws IsDispatchingException
     */
    protected function _dispatch(ActionInterface $action): ActionInterface
    {
        if (true === $this->isDispatching) {
            throw new IsDispatchingException();
        }

        try {
            $this->isDispatching = true;
            $startState = $this->getState();
            $endState = $this->reduce($startState, $action);
        } finally {
            $this->isDispatching = false;
        }

        if (false === $this->statesAreEqual($startState, $endState)) {
            $this->setState($endState);
            $this->emitChange();
        }

        return $action;
    }

    /**
     * Set the state.
     *
     * @param $state
     */
    protected function setState($state): void
    {
        $this->state = $state;
    }

    /**
     * Test whether the given states are equal.
     *
     * @param $a
     * @param $b
     * @return bool
     */
    protected function statesAreEqual($a, $b): bool
    {
        return $a == $b;
    }

    /**
     * Call the reducer.
     *
     * @param $state
     * @param ActionInterface $action
     * @return mixed
     */
    protected function reduce($state, ActionInterface $action)
    {
        return ($this->reducer)($state, $action);
    }

    /**
     * Emit a change event.
     */
    protected function emitChange(): void
    {
        $this->emitter->emit(self::EVENT_CHANGE, [$this]);
    }

    /**
     * Dispatch an initializing action.
     *
     * @throws IsDispatchingException
     */
    protected function initialize(): void
    {
        $this->dispatch(new Action(self::ACTION_TYPE_INIT));
    }
}