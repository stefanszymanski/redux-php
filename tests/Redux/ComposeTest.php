<?php
declare(strict_types=1);

namespace test\Ctefan\Redux\Redux;

use Ctefan\Redux\Redux;
use test\Ctefan\Redux\AbstractTestCase;

class ComposeTest extends AbstractTestCase
{
    public function testReturnsFunctionIfNoArgumentsAreGiven(): void
    {
        $callable = Redux::compose();
        $this::assertTrue(is_callable($callable));
    }

    public function testReturnsGivenFunctionIfOneArgumentIsGiven(): void
    {
        $callable = function(){};
        $result = Redux::compose($callable);
        $this::assertSame($callable, $result);
    }

    public function testReturnsNewFunctionIfMoreThanOneArgumentIsGiven(): void
    {
        $callable = function(){};
        $result = Redux::compose($callable, $callable);
        $this::assertNotSame($callable, $result);
        $this::assertTrue(is_callable($result));
    }
}