<?php
declare(strict_types=1);

namespace test\Ctefan\Redux\Redux;

use Ctefan\Redux\Redux;
use Ctefan\Redux\Action\Action;
use test\Ctefan\Redux\AbstractTestCase;

class CreateStoreTest extends AbstractTestCase
{
    public function testPassInitialState(): void
    {
        $reducer = function($state) {
            return $state;
        };
        $initialState = [
            'a' => 123,
            'b' => 456,
        ];
        $store = Redux::createStore($reducer, $initialState);

        $this::assertEquals($initialState, $store->getState());
    }

    public function testAppliesTheReducer(): void
    {
        $reducer = $this->getTestReducer();
        $store = Redux::createStore($reducer);

        $this::assertEquals([], $store->getState());

        $store->dispatch(new Action('unknown', 100));
        $this::assertEquals([], $store->getState());

        $store->dispatch(new Action('add', 5));
        $this::assertEquals([5], $store->getState());

        $store->dispatch(new Action('add', 8));
        $this::assertEquals([5, 8], $store->getState());
    }

    public function testPreservesStateWhenReplacingTheReducer(): void
    {
        $store = Redux::createStore($this->getTestReducer());

        $store->dispatch(new Action('add', 5));
        $store->dispatch(new Action('add', 8));
        $this::assertEquals([5, 8], $store->getState());

        $store->setReducer($this->getAnotherTestReducer());
        $this::assertEquals([5, 8], $store->getState());

        $store->dispatch(new Action('add', 10));
        $this::assertEquals([10, 5, 8], $store->getState());

        $store->setReducer($this->getTestReducer());
        $this::assertEquals([10, 5, 8], $store->getState());

        $store->dispatch(new Action('add', 6));
        $this::assertEquals([10, 5, 8, 6], $store->getState());
    }

    public function testCanAddAndRemoveSubscriber(): void
    {
        $store = Redux::createStore($this->getTestReducer());
        $listener = $this->getMockCallable();

        $listener->expects($this->once())->method('__invoke');

        $unsubscribe = $store->subscribe($listener);
        $store->dispatch(new Action('add', 10));
        $unsubscribe();
        $store->dispatch(new Action('add', 10));
    }

    public function testSupportsMultipleSubscribers(): void
    {
        $store = Redux::createStore($this->getTestReducer());
        $listenerA = $this->getMockCallable();
        $listenerB = $this->getMockCallable();

        $listenerA->expects($this->exactly(4))->method('__invoke');
        $listenerB->expects($this->exactly(2))->method('__invoke');

        $store->subscribe($listenerA);
        $store->dispatch(new Action('add', 5));
        $store->dispatch(new Action('add', 5));
        $store->subscribe($listenerB);
        $store->dispatch(new Action('add', 5));
        $store->dispatch(new Action('add', 5));
    }

    public function testDoesOnlyEmitOnChange(): void
    {
        $store = Redux::createStore($this->getTestReducer());
        $listener = $this->getMockCallable();

        $listener->expects($this->once())->method('__invoke');

        $store->subscribe($listener);
        $store->dispatch(new Action('unknown', 10));
        $store->dispatch(new Action('add', 10));
    }

    public function testCanUnsubscribeMultipleTimes(): void
    {
        $store = Redux::createStore($this->getTestReducer());
        $listener = $this->getMockCallable();

        $unsubscribe = $store->subscribe($listener);
        $this::assertTrue($unsubscribe());
        $this::assertFalse($unsubscribe());
        $this::assertFalse($unsubscribe());
    }

}