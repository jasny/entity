<?php

namespace Jasny\Entity\Tests\Collection;

use Jasny\Entity\EntityInterface;
use Jasny\Entity\Collection\EntitySet;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Collection\EntitySet
 * {@internal AbstractCollection is covered by EntityListTest}
 */
class EntitySetTest extends TestCase
{
    /**
     * @var EntitySet
     */
    protected $collection;

    /**
     * @var EntityInterface[]|MockObject[]
     */
    protected $entities;

    public function setUp(): void
    {
        $this->entities = [
            $this->createMock(EntityInterface::class),
            $this->createMock(EntityInterface::class),
            $this->createMock(EntityInterface::class)
        ];

        $this->collection = (new EntitySet())->withEntities($this->entities);
    }

    public function testCreate()
    {
        $this->entities[0]->expects($this->exactly(2))->method('is')
            ->withConsecutive(
                [$this->identicalTo($this->entities[1])],
                [$this->identicalTo($this->entities[2])]
            )
            ->willReturn(false);
        $this->entities[1]->expects($this->once())->method('is')
            ->with($this->identicalTo($this->entities[2]))
            ->willReturn(false);
        $this->entities[2]->expects($this->never())->method('is');

        $set = (new EntitySet())->withEntities($this->entities);

        $this->assertCount(3, $set);
        $this->assertSame($this->entities, $this->collection->toArray());
    }

    public function testCreateWithDuplicate()
    {
        $this->entities[0]->expects($this->exactly(2))->method('is')
            ->withConsecutive(
                [$this->identicalTo($this->entities[1])],
                [$this->identicalTo($this->entities[2])]
            )
            ->willReturnOnConsecutiveCalls(true, false);
        $this->entities[1]->expects($this->never())->method('is');
        $this->entities[2]->expects($this->never())->method('is');

        $collection = (new EntitySet())->withEntities($this->entities);

        $this->assertCount(2, $collection);
        $this->assertSame([$this->entities[0], $this->entities[2]], $collection->toArray());
    }

    public function testAdd()
    {
        $newEntity = $this->createMock(EntityInterface::class);

        foreach ($this->entities as $entity) {
            $entity->expects($this->once())->method('is')->with($this->identicalTo($newEntity))->willReturn(false);
        }

        $this->collection->add($newEntity);

        $this->assertCount(4, $this->collection);
        $this->assertSame(array_merge($this->entities, [$newEntity]), $this->collection->toArray());
    }

    public function testAddExisting()
    {
        $newEntity = $this->createMock(EntityInterface::class);

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
        $find = $this->createMock(EntityInterface::class);

        $this->entities[0]->expects($this->once())->method('is')->with($this->identicalTo($find))->willReturn(false);
        $this->entities[1]->expects($this->once())->method('is')->with($this->identicalTo($find))->willReturn(true);
        $this->entities[2]->expects($this->never())->method('is');

        $this->collection->remove($find);

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
