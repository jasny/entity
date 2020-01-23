<?php

namespace Jasny\Entity\Tests\Collection;

use Jasny\Entity\Collection\EntityMap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Jasny\Entity\EntityInterface;

/**
 * @covers \Jasny\Entity\Collection\EntityMap
 * {@internal AbstractCollection is covered by EntityMapTest}
 */
class EntityMapTest extends TestCase
{
    /**
     * @var EntityMap
     */
    protected $collection;

    /**
     * @var EntityInterface[]|MockObject[]
     */
    protected $entities;

    /**
     * Set up dependencies
     */
    public function setUp(): void
    {
        $this->entities = [
            'one' => $this->createMock(EntityInterface::class),
            'two' => $this->createMock(EntityInterface::class),
            'three' => $this->createMock(EntityInterface::class)
        ];

        $this->collection = (new EntityMap())->withEntities($this->entities);
    }


    public function testCreate()
    {
        $this->assertEquals(EntityInterface::class, $this->collection->getType());

        $this->assertSame($this->entities, $this->collection->toArray());
        $this->assertSame($this->entities, iterator_to_array($this->collection));
    }


    public function testOffsetExists()
    {
        $this->assertTrue(isset($this->collection['one']));
        $this->assertFalse(isset($this->collection['not_exist']));
    }

    public function testOffsetGet()
    {
        $this->assertSame($this->entities['two'], $this->collection['two']);
    }

    public function testOffsetGetIndexNotExists()
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage("Key 'not_exist' does not exist in map");

        $this->collection['not_exist'];
    }


    public function testOffsetSet()
    {
        $entity = $this->createMock(EntityInterface::class);
        $this->collection['foo'] = $entity;

        $this->assertCount(4, $this->collection->toArray());
        $this->assertContains($entity, $this->collection->toArray());
        $this->assertSame($entity, $this->collection['foo']);
    }

    public function testOffsetSetOverwrite()
    {
        $entity = $this->createMock(EntityInterface::class);
        $this->collection['two'] = $entity;

        $this->assertCount(3, $this->collection->toArray());
        $this->assertContains($entity, $this->collection->toArray());
        $this->assertNotContains($this->entities['two'], $this->collection->toArray());
        $this->assertSame($entity, $this->collection['two']);
    }

    public function testOffsetSetNoIndex()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("Key must be specified when adding an entity to a map");

        $this->collection[] = $this->createMock(EntityInterface::class);
    }

    public function testOffsetUnset()
    {
        unset($this->collection['two']);

        $this->assertCount(2, $this->collection->toArray());
        $this->assertNotContains($this->entities['two'], $this->collection->toArray());
    }

    public function testOffsetUnsetIndexNotExist()
    {
        unset($this->collection['foo']);

        $this->assertCount(3, $this->collection->toArray());
    }
}
