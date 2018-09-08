<?php

namespace Jasny\Tests\EntityCollection\Traits;

use PHPUnit\Framework\TestCase;
use Jasny\EntityCollection\Traits\ArrayAccessTrait;

/**
 * @covers Jasny\EntityCollection\Traits\ArrayAccessTrait
 * @group collection
 */
class ArrayAccessTraitTest extends TestCase
{
    /**
     * Collection trait mock
     **/
    public $collection;

    /**
     * Set up dependencies before each test
     */
    public function setUp()
    {
        $this->collection = $this->getMockForTrait(ArrayAccessTrait::class);
    }

    /**
     * Provide data for testing 'offsetExists' method
     *
     * @return array
     */
    public function offsetExistsProvider()
    {
        $entities = [1, 2, 3];

        return [
            [$entities, 0, true],
            [$entities, 1, true],
            [$entities, 2, true],
            [$entities, 3, false],
            [$entities, -1, false],
            [$entities, 'foo', false],
            [[], 1, false]
        ];
    }

    /**
     * Test 'offsetExists' method
     *
     * @dataProvider offsetExistsProvider
     */
    public function testOffsetExists($entities, $index, $expected)
    {
        $this->collection->entities = $entities;
        $result = $this->collection->offsetExists($index);

        $this->assertSame($expected, $result);
    }

    /**
     * Test 'offsetGet' method
     */
    public function testOffsetGet()
    {
        $this->collection->entities = ['foo', 'bar', 'baz'];
        $this->collection->expects($this->once())->method('assertIndex')->with(1);

        $result = $this->collection->offsetGet(1);

        $this->assertSame('bar', $result);
    }

    /**
     * Provide data for testing 'offsetSet' method
     *
     * @return array
     */
    public function offsetSetProvider()
    {
        return [
            [[1, 2, 3], 0, ['foo', 2, 3]],
            [[1, 2, 3], 1, [1, 'foo', 3]],
            [[1, 2, 3], 2, [1, 2, 'foo']],
            [[1, 2, 3], null, [1, 2, 3, 'foo']],
            [[], null, ['foo']]
        ];
    }

    /**
     * Test 'offsetSet' method
     *
     * @dataProvider offsetSetProvider
     */
    public function testOffsetSet($entities, $index, $expected)
    {
        $this->collection->entities = $entities;
        if (isset($index)) {
            $this->collection->expects($this->once())->method('assertIndex')->with($index, true);
        }

        $this->collection->offsetSet($index, 'foo');

        $this->assertSame($expected, $this->collection->entities);
    }

    /**
     * Test 'offsetUnset' method
     */
    public function testOffsetUnset()
    {
        $this->collection->entities = ['foo', 'bar', 'baz'];
        $this->collection->expects($this->once())->method('assertIndex')->with(1);

        $this->collection->offsetUnset(1);

        $this->assertSame(['foo', 'baz'], $this->collection->entities);
    }
}
