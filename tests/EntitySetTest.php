<?php

namespace Jasny\EntityCollection\Tests;

use Jasny\Entity\Entity;
use Jasny\Entity\IdentifiableEntity;
use Jasny\EntityCollection\EntitySet;
use Jasny\TestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers Jasny\EntityCollection\EntitySet
 */
class EntitySetTest extends TestCase
{
    use TestHelper;

    /**
     * @var EntitySet
     */
    protected $collection;

    /**
     * @var IdentifiableEntity[]|MockObject[]
     */
    protected $entities;

    /**
     * Set up dependencies
     */
    public function setUp()
    {
        $this->entities = [
            $this->createConfiguredMock(IdentifiableEntity::class, ['getId' => 'one']),
            $this->createConfiguredMock(IdentifiableEntity::class, ['getId' => 'two']),
            $this->createConfiguredMock(IdentifiableEntity::class, ['getId' => null])
        ];

        $this->collection = (new EntitySet(IdentifiableEntity::class))
            ->withEntities($this->entities);
    }

    public function testCreate()
    {
        $this->assertCount(3, $this->collection);
        $this->assertSame($this->entities, $this->collection->toArray());
    }

    public function testCreateDuplicate()
    {
        $entities = array_merge(
            $this->entities,
            [$this->createConfiguredMock(IdentifiableEntity::class, ['getId' => 'one'])]
        );
        $collection = (new EntitySet(IdentifiableEntity::class))
            ->withEntities($entities);

        $this->assertCount(3, $collection);
        $this->assertSame($this->entities, $collection->toArray());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Jasny\Entity\Entity does not implement Jasny\Entity\IdentifiableEntity
     */
    public function testCreateNonIndentifiable()
    {
        new EntitySet(Entity::class);
    }


    public function testUnique()
    {
        $result = $this->collection->unique();
        $this->assertSame($this->collection, $result);
    }

    public function testAdd()
    {
        $newEntity = $this->createMock(IdentifiableEntity::class);

        foreach ($this->entities as $entity) {
            $entity->expects($this->once())->method('is')->with($this->identicalTo($newEntity))->willReturn(false);
        }

        $this->collection->add($newEntity);

        $this->assertCount(4, $this->collection);
        $this->assertSame(array_merge($this->entities, [$newEntity]), $this->collection->toArray());
    }

    public function testAddExisting()
    {
        $newEntity = $this->createMock(IdentifiableEntity::class);

        $this->entities[0]->expects($this->once())->method('is')
            ->with($this->identicalTo($newEntity))->willReturn(false);
        $this->entities[1]->expects($this->once())->method('is')
            ->with($this->identicalTo($newEntity))->willReturn(true);
        $this->entities[2]->expects($this->never())->method('is');

        $this->collection->add($newEntity);

        $this->assertCount(3, $this->collection);
        $this->assertSame($this->entities + [1 => $newEntity], $this->collection->toArray());
    }


    public function testRemoveById()
    {
        $this->entities[0]->expects($this->once())->method('is')->with(42)->willReturn(false);
        $this->entities[1]->expects($this->once())->method('is')->with(42)->willReturn(true);
        $this->entities[2]->expects($this->never())->method('is');

        $this->collection->remove(42);

        $this->assertCount(2, $this->collection);
        $this->assertSame([$this->entities[0], $this->entities[2]], $this->collection->toArray());
    }

    public function testRemoveByRef()
    {
        $newEntity = $this->createMock(IdentifiableEntity::class);

        $this->entities[0]->expects($this->once())->method('is')
            ->with($this->identicalTo($newEntity))->willReturn(false);
        $this->entities[1]->expects($this->once())->method('is')
            ->with($this->identicalTo($newEntity))->willReturn(true);
        $this->entities[2]->expects($this->never())->method('is');

        $this->collection->remove($newEntity);

        $this->assertCount(2, $this->collection);
        $this->assertSame([$this->entities[0], $this->entities[2]], $this->collection->toArray());
    }

    public function testRemoveNotExist()
    {
        foreach ($this->entities as $entity) {
            $entity->expects($this->once())->method('is')->with(42)->willReturn(false);
        }

        $this->collection->remove(42);

        $this->assertCount(3, $this->collection);
        $this->assertSame($this->entities, $this->collection->toArray());
    }
}
