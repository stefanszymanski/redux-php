<?php
declare(strict_types=1);

namespace Ctefan\Redux\Store;

interface EnhanceableStoreInterface extends StoreInterface
{
    public function getDispatcher(): callable;

    public function setDispatcher(callable $dispatcher): void;
}