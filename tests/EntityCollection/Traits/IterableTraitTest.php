<?php

namespace Jasny\EntityCollection\Traits\Tests;

use PHPUnit\Framework\TestCase;
use Jasny\EntityInterface;
use Jasny\EntityCollection\Traits\IterableTrait;

/**
 * @covers Jasny\EntityCollection\Traits\IterableTrait
 */
class IterableTraitTest extends TestCase
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
        $this->collection = $this->getMockForTrait(IterableTrait::class);
    }

    /**
     * Test 'getIterator' method
     */
    public function testGetIterator()
    {
        $entities = [1, 2, 3];
        $this->collection->entities = $entities;

        $result = $this->collection->getIterator();

        $this->assertInstanceOf(\ArrayIterator::class, $result);
        $this->assertSame($entities, $result->getArrayCopy());
    }

    /**
     * Test 'toArray' method
     */
    public function testToArray()
    {
        $entities = [1, 2, 3];
        $this->collection->entities = $entities;

        $result = $this->collection->toArray();

        $this->assertSame($entities, $result);
    }
}
