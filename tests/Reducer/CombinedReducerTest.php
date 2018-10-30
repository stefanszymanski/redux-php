<?php
declare(strict_types=1);

namespace test\Ctefan\Redux\Reducer;

use Ctefan\Redux\Action\Action;
use Ctefan\Redux\Action\ActionInterface;
use Ctefan\Redux\Reducer\CombinedReducer;
use test\Ctefan\Redux\AbstractTestCase;

class CombinedReducerTest extends AbstractTestCase
{
    public function testMapsKeysToReducers(): void
    {
        $reducerA = function($state, ActionInterface $action) {
            $state++;
            return $state;
        };
        $reducerB = function($state, ActionInterface $action) {
            $state--;
            return $state;
        };
        $rootReducer = new CombinedReducer([
            'a' => $reducerA,
            'b' => $reducerB,
        ]);

        $state = $rootReducer(['a' => 10, 'b' => 10], new Action('test'));
        $this::assertEquals(['a' => 11, 'b' => 9], $state);
    }
}