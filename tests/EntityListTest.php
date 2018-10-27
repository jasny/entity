<?php

namespace Jasny\EntityCollection\Tests;

use Jasny\Entity\AbstractBasicEntity;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Jasny\Entity\Entity;
use Jasny\EntityCollection\EntityList;

/**
 * @covers \Jasny\EntityCollection\EntityList
 * @covers \Jasny\EntityCollection\EntityCollection
 */
class EntityListTest extends TestCase
{
    use \Jasny\TestHelper;

    /**
     * @var EntityMap
     */
    protected $collection;

    /**
     * @var Entity[]|MockObject[]
     */
    protected $entities;

    /**
     * Set up dependencies
     */
    public function setUp()
    {
        $this->entities = [
            $this->createMock(Entity::class),
            $this->createMock(Entity::class),
            $this->createMock(Entity::class)
        ];

        $this->collection = (new EntityList(Entity::class))
            ->withEntities($this->entities);
    }

    public function testCreate()
    {
        $entities = array_combine([0, 1, 27], $this->entities);

        $list = (new EntityList(Entity::class))
            ->withEntities($entities);

        $this->assertSame($this->entities, iterator_to_array($list));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage NotExist does not implement Jasny\Entity\Entity
     */
    public function testCreateInvalidClass()
    {
        new EntityList('NotExist');
    }

    public function testGetEntityClass()
    {
        $list = new EntityList(AbstractBasicEntity::class);

        $this->assertEquals(AbstractBasicEntity::class, $list->getEntityClass());
    }

    public function testAdd()
    {
        $newEntity = $this->createMock(Entity::class);
        $this->collection->add($newEntity);

        $this->assertCount(4, $this->collection);
        $this->assertSame(array_merge($this->entities, [$newEntity]), $this->collection->toArray());
    }

    public function testRemoveById()
    {
        $this->entities[0]->expects($this->once())->method('is')->with(42)->willReturn(false);
        $this->entities[1]->expects($this->once())->method('is')->with(42)->willReturn(true);
        $this->entities[2]->expects($this->once())->method('is')->with(42)->willReturn(false);

        $this->collection->remove(42);

        $this->assertCount(2, $this->collection);
        $this->assertSame([$this->entities[0], $this->entities[2]], $this->collection->toArray());
    }

    public function testRemoveByRef()
    {
        $this->entities[0]->expects($this->once())->method('is')
            ->with($this->identicalTo($this->entities[1]))->willReturn(false);
        $this->entities[1]->expects($this->once())->method('is')
            ->with($this->identicalTo($this->entities[1]))->willReturn(true);
        $this->entities[2]->expects($this->once())->method('is')
            ->with($this->identicalTo($this->entities[1]))->willReturn(false);

        $this->collection->remove($this->entities[1]);

        $this->assertCount(2, $this->collection);
        $this->assertSame([$this->entities[0], $this->entities[2]], $this->collection->toArray());
    }

    public function testRemoveMultiple()
    {
        $this->entities[0]->expects($this->once())->method('is')->with(42)->willReturn(false);
        $this->entities[1]->expects($this->once())->method('is')->with(42)->willReturn(true);
        $this->entities[2]->expects($this->once())->method('is')->with(42)->willReturn(true);

        $this->collection->remove(42);

        $this->assertCount(1, $this->collection);
        $this->assertSame([$this->entities[0]], $this->collection->toArray());
    }


    public function testRemoveNotExist()
    {
        $this->entities[0]->expects($this->once())->method('is')->with(42)->willReturn(false);
        $this->entities[1]->expects($this->once())->method('is')->with(42)->willReturn(false);
        $this->entities[2]->expects($this->once())->method('is')->with(42)->willReturn(false);

        $this->collection->remove(42);

        $this->assertCount(3, $this->collection);
        $this->assertSame($this->entities, $this->collection->toArray());
    }
}
