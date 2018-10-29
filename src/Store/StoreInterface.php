<?php
declare(strict_types=1);

namespace Ctefan\Redux\Store;

use Ctefan\Redux\Action\ActionInterface;

interface StoreInterface
{
    public function dispatch(ActionInterface $action): ActionInterface;

    public function getState();

    public function subscribe(callable $callback): callable;

    public function setReducer(callable $reducer): void;

    public function setDispatcher(callable $dispatcher): void;
}