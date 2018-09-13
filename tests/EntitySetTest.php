<?php

namespace Jasny\EntityCollection\Tests;

use Jasny\Entity\EntityInterface;
use Jasny\Entity\IdentifiableEntityInterface;
use Jasny\TestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Jasny\Entity\Entity;
use Jasny\EntityCollection\EntitySet;

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
     * @var IdentifiableEntityInterface[]|MockObject[]
     */
    protected $entities;

    /**
     * Set up dependencies
     */
    public function setUp()
    {
        $this->entities = [
            $this->createConfiguredMock(IdentifiableEntityInterface::class, ['getId' => 'one']),
            $this->createConfiguredMock(IdentifiableEntityInterface::class, ['getId' => 'two']),
            $this->createConfiguredMock(IdentifiableEntityInterface::class, ['getId' => null])
        ];

        $this->collection = (new EntitySet(IdentifiableEntityInterface::class))
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
            [$this->createConfiguredMock(IdentifiableEntityInterface::class, ['getId' => 'one'])]
        );
        $collection = (new EntitySet(IdentifiableEntityInterface::class))
            ->withEntities($entities);

        $this->assertCount(3, $collection);
        $this->assertSame($this->entities, $collection->toArray());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Jasny\Entity\EntityInterface does not implement Jasny\Entity\IdentifiableEntityInterface
     */
    public function testCreateNonIndentifiable()
    {
        new EntitySet(EntityInterface::class);
    }


    public function testUnique()
    {
        $result = $this->collection->unique();
        $this->assertSame($this->collection, $result);
    }

    public function testAdd()
    {
        $newEntity = $this->createMock(IdentifiableEntityInterface::class);

        foreach ($this->entities as $entity) {
            $entity->expects($this->once())->method('is')->with($this->identicalTo($newEntity))->willReturn(false);
        }

        $this->collection->add($newEntity);

        $this->assertCount(4, $this->collection);
        $this->assertSame(array_merge($this->entities, [$newEntity]), $this->collection->toArray());
    }

    public function testAddExisting()
    {
        $newEntity = $this->createMock(IdentifiableEntityInterface::class);

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
        $newEntity = $this->createMock(IdentifiableEntityInterface::class);

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
