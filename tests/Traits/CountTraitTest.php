<?php

namespace Jasny\EntityCollection\Tests\Traits;

use Jasny\Entity\EntityInterface;
use Jasny\TestHelper;
use PHPUnit\Framework\TestCase;
use Jasny\EntityCollection\Traits\CountTrait;

/**
 * @covers \Jasny\EntityCollection\Traits\CountTrait
 */
class CountTraitTest extends TestCase
{
    use TestHelper;

    /**
     * @var MockObject|CountTrait
     */
    public $collection;

    /**
     * Set up dependencies before each test
     */
    public function setUp()
    {
        $this->collection = $this->getMockForTrait(CountTrait::class);
    }

    /**
     * Provide data for testing 'count' method
     *
     * @return array
     */
    public function countProvider()
    {
        $entities = [
            $this->createMock(EntityInterface::class),
            $this->createMock(EntityInterface::class),
            $this->createMock(EntityInterface::class)
        ];

        return [
            [$entities, 3],
            [[], 0]
        ];
    }

    /**
     * Test 'count' method
     *
     * @dataProvider countProvider
     */
    public function testCount($entities, $expected)
    {
        $this->setPrivateProperty($this->collection, 'entities', $entities);

        $this->assertSame($expected, $this->collection->count());
    }

    /**
     * Test 'countTotal' method
     */
    public function testCountTotal()
    {
        $this->setPrivateProperty($this->collection, 'totalCount', 42);

        $this->assertSame(42, $this->collection->countTotal());
    }

    /**
     * Test 'countTotal' method with closure
     */
    public function testCountTotalWithClosure()
    {
        $closure = $this->createCallbackMock($this->once(), [], 21);

        $this->setPrivateProperty($this->collection, 'totalCount', $closure);

        $this->assertSame(21, $this->collection->countTotal());

        // Closure should not be called twice
        $this->assertSame(21, $this->collection->countTotal());
    }

    /**
     * Test 'countTotal' method, in case when totalCount proerpty is not set
     *
     * @expectedException \BadMethodCallException
     */
    public function testCountTotalNotSet()
    {
        $this->collection->countTotal();
    }

    /**
     * Test 'countTotal' method with closure that returns a negative number
     *
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Failed to get total count: Expected a positive integer, got -1
     */
    public function testCountTotalWithClosureNegative()
    {
        $closure = $this->createCallbackMock($this->once(), [], -1);
        $this->setPrivateProperty($this->collection, 'totalCount', $closure);

        $this->collection->countTotal();
    }

    /**
     * Test 'countTotal' method with closure that returns a string
     *
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Failed to get total count: Expected a positive integer, got string
     */
    public function testCountTotalWithClosureString()
    {
        $closure = $this->createCallbackMock($this->once(), [], 'foo');
        $this->setPrivateProperty($this->collection, 'totalCount', $closure);

        $this->collection->countTotal();
    }
}
