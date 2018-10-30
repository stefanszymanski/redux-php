<?php
declare(strict_types=1);

namespace test\Ctefan\Redux;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Ctefan\Redux\Action\ActionInterface;

abstract class AbstractTestCase extends TestCase
{

    protected function getTestReducer(): callable
    {
        return function($state, ActionInterface $action) {
            switch ($action->getType()) {
                case 'add':
                    $state[] = $action->getPayload();
                    break;
                case 'subtract':
                    $state[] = -$action->getPayload();
                    break;
            }
            return $state;
        };
    }

    protected function getAnotherTestReducer(): callable
    {
        return function($state, ActionInterface $action) {
            switch ($action->getType()) {
                case 'add':
                    array_unshift($state, $action->getPayload());
                    break;
                case 'subtract':
                    array_unshift($state, -$action->getPayload());
                    break;
            }
            return $state;
        };
    }

    protected function getMockCallable(): MockObject
    {
        return $this->getMockBuilder(stdClass::class)
            ->setMethods(['__invoke'])
            ->getMock();
    }
}