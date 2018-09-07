<?php

namespace Jasny\Tests\EntityCollection\Traits;

use PHPUnit\Framework\TestCase;
use Jasny\EntityCollection\Traits\CountTrait;

/**
 * @covers Jasny\EntityCollection\Traits\CountTrait
 */
class CountTraitTest extends TestCase
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
        $this->collection = $this->getMockForTrait(CountTrait::class);
    }

    /**
     * Provide data for testing 'count' method
     *
     * @return array
     */
    public function countProvider()
    {
        return [
            [[1, 2, 3], 3],
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
        $this->collection->entities = $entities;

        $result = $this->collection->count();

        $this->assertSame($expected, $result);
    }

    /**
     * Provide data for testing 'countTotal' method
     *
     * @return array
     */
    public function countTotalProvider()
    {
        $closure = function() {
            return 10;
        };

        return [
            [3, 3],
            [0, 0],
            [$closure, 10],
        ];
    }

    /**
     * Test 'countTotal' method
     *
     * @dataProvider countTotalProvider
     */
    public function testCountTotal($totalCount, $expected)
    {
        $this->collection->totalCount = $totalCount;

        $result = $this->collection->countTotal();

        $this->assertSame($expected, $result);
    }

    /**
     * Test 'countTotal' method, in case when totalCount proerpty is not set
     */
    public function testCountTotalNotSet()
    {
        $collection = $this->getMockForTrait(CountTrait::class, [], '', true, true, true, ['count']);

        $collection->totalCount = null;
        $collection->expects($this->once())->method('count')->willReturn(12);

        $result = $collection->countTotal();

        $this->assertSame(12, $result);
    }
}
