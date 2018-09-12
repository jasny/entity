<?php

namespace Jasny\EntityCollection\Tests;

use PHPUnit\Framework\TestCase;
use Jasny\EntityCollection\EntityMap;
use Jasny\Entity\Entity;
use Jasny\Entity\EntityInterface;

/**
 * @covers \Jasny\EntityCollection\EntityMap
 */
class EntityMapTest extends TestCase
{
    use \Jasny\TestHelper;

    /**
     * @var EntityMap
     */
    protected $collection;

    /**
     * @var EntityInterface[]
     */
    protected $entities;

    /**
     * Set up dependencies
     */
    public function setUp()
    {
        $this->entities = [
            'one' => $this->createMock(EntityInterface::class),
            'two' => $this->createMock(EntityInterface::class),
            'three' => $this->createMock(EntityInterface::class)
        ];

        $this->collection = (new EntityMap(EntityInterface::class))
            ->withEntities($this->entities);
    }

    /**
     * Test 'offsetExists' method
     */
    public function testOffsetExists()
    {
        $this->assertTrue(isset($this->collection['one']));
        $this->assertFalse(isset($this->collection['not_exist']));
    }

    /**
     * Test 'offsetGet' method
     */
    public function testOffsetGet()
    {
        $this->assertSame($this->entities['two'], $this->collection['two']);
    }

    /**
     * Test 'offsetGet' method, in case when index does not exists in collection
     *
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Key 'not_exist' does not exist in map
     */
    public function testOffsetGetIndexNotExists()
    {
        $this->collection['not_exist'];
    }

    /**
     * Test 'offsetGet' method, in case when index is not a string
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Key must be a string, integer given
     */
    public function testOffsetGetIntIndex()
    {
        $foo = $this->collection[42];
    }


    /**
     * Test 'offsetSet' method
     */
    public function testOffsetSet()
    {
        $entity = $this->createMock(EntityInterface::class);
        $this->collection['foo'] = $entity;

        $this->assertCount(4, $this->collection->toArray());
        $this->assertContains($entity, $this->collection->toArray());
        $this->assertSame($entity, $this->collection['foo']);
    }

    /**
     * Test 'offsetSet' method
     */
    public function testOffsetSetOverwrite()
    {
        $entity = $this->createMock(EntityInterface::class);
        $this->collection['two'] = $entity;

        $this->assertCount(3, $this->collection->toArray());
        $this->assertContains($entity, $this->collection->toArray());
        $this->assertNotContains($this->entities['two'], $this->collection->toArray());
        $this->assertSame($entity, $this->collection['two']);
    }

    /**
     * Test 'offsetSet' method, in case when index is null
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Key must be a string, integer given
     */
    public function testOffsetSetIntIndex()
    {
        $this->collection[42] = $this->createMock(EntityInterface::class);
    }

    /**
     * Test 'offsetSet' method, in case when index is null
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Key must be a string, NULL given
     */
    public function testOffsetSetNullIndex()
    {
        $this->collection[] = $this->createMock(EntityInterface::class);
    }

    /**
     * Test 'offsetUnset' method
     */
    public function testOffsetUnset()
    {
        unset($this->collection['two']);

        $this->assertCount(2, $this->collection->toArray());
        $this->assertNotContains($this->entities['two'], $this->collection->toArray());
    }

    /**
     * Test 'offsetUnset' method
     */
    public function testOffsetUnsetIndexNotExist()
    {
        unset($this->collection['foo']);

        $this->assertCount(3, $this->collection->toArray());
    }

    /**
     * Test 'offsetUnset' method, in case when index does not exists in collection
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Key must be a string, integer given
     */
    public function testOffsetUnsetIntIndex()
    {
        unset($this->collection[42]);
    }
}
