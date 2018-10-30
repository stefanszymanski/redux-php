<?php
declare(strict_types=1);

namespace test\Ctefan\Redux\Reducer;

use Ctefan\Redux\Action\Action;
use Ctefan\Redux\Action\ActionInterface;
use Ctefan\Redux\Reducer\ComposedReducer;
use test\Ctefan\Redux\AbstractTestCase;

class ComposedReducerTest extends AbstractTestCase
{
    public function testExecutesAllReducers(): void
    {
        $reducerA = function($state, ActionInterface $action) {
            $state += 3;
            return $state;
        };
        $reducerB = function($state, ActionInterface $action) {
            $state += 5;
            return $state;
        };
        $rootReducer = new ComposedReducer([$reducerA, $reducerB]);

        $state = $rootReducer(0, new Action('test'));
        $this::assertEquals(8, $state);
    }
}