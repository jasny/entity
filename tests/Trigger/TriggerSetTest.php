<?php

namespace Jasny\Entity\Tests\Trigger;

use Jasny\Entity\EntityInterface;
use Jasny\Entity\Handler\HandlerInterface;
use Jasny\Entity\Trigger\TriggerSet;
use Jasny\TestHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Trigger\TriggerSet
 */
class TriggerSetTest extends TestCase
{
    use TestHelper;

    public function testWith()
    {
        $setEmpty = new TriggerSet();

        $foo = [
            'first' => $this->createMock(HandlerInterface::class),
            'second' => function() {}
        ];
        $bar = [
            'first' => function() {}
        ];

        $setFoo = $setEmpty->with('foo', $foo);
        $this->assertInstanceOf(TriggerSet::class, $setFoo);
        $this->assertNotSame($setEmpty, $setFoo);
        $this->assertAttributeSame(compact('foo'), 'triggers', $setFoo);

        $setFooBar = $setFoo->with('bar', $bar);
        $this->assertInstanceOf(TriggerSet::class, $setFooBar);
        $this->assertNotSame($setFoo, $setFooBar);
        $this->assertAttributeSame(compact('foo', 'bar'), 'triggers', $setFooBar);

        return $setFooBar;
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unable to add 'foo' trigger(s); Expected Jasny\Entity\Handler\HandlerInterface or (non-object) callable, got a class@anonymous
     */
    public function testWithNonHandlerCallableObject()
    {
        $setEmpty = new TriggerSet();

        $invoke = new class() {
            public function __invoke()
            {
            }
        };

        $setEmpty->with('foo', ['first' => $invoke]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unable to add 'foo' trigger(s); Expected Jasny\Entity\Handler\HandlerInterface or (non-object) callable, got a string
     */
    public function testWithUncallable()
    {
        $setEmpty = new TriggerSet();

        $setEmpty->with('foo', ['first' => 'non_existent']);
    }

    /**
     * @depends testWith
     */
    public function testHas(TriggerSet $set)
    {
        $this->assertTrue($set->has('bar'));
        $this->assertFalse($set->has('non_existing'));
    }

    /**
     * @depends testWith
     * @depends testHas
     */
    public function testWithout(TriggerSet $setFooBar)
    {
        $setFoo = $setFooBar->without('bar');

        $this->assertNotSame($setFooBar, $setFoo);

        $this->assertTrue($setFoo->has('foo'));
        $this->assertFalse($setFoo->has('bar'));
    }

    /**
     * @depends testWith
     */
    public function testWithoutNonExisting(TriggerSet $setFooBar)
    {
        $set = $setFooBar->without('non_existing');

        $this->assertSame($setFooBar, $set);
    }

    /**
     * @depends testWith
     */
    public function testGet()
    {
        $bar = [
            'first' => function() {}
        ];

        $setEmpty = new TriggerSet();
        $setBar = $setEmpty->with('bar', $bar);

        $this->assertSame($bar, $setBar->get('bar'));
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetNonExisting()
    {
        $setEmpty = new TriggerSet();

        $setEmpty->get('non_existing');
    }

    /**
     * @depends testWith
     */
    public function testApply(TriggerSet $setFooBar)
    {
        $entity = $this->createMock(EntityInterface::class);
        $entity->expects($this->exactly(3))->method('on')
            ->withConsecutive(
                ['first', $this->isInstanceOf(HandlerInterface::class)],
                ['second', $this->isInstanceOf(\Closure::class)],
                ['first', $this->isInstanceOf(\Closure::class)]
            )
            ->willReturnSelf();

        $setFooBar->apply($entity);
    }
}
