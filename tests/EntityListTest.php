<?php

namespace Jasny\Tests\EntityCollection;

use Jasny\Entity\AbstractEntity;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Jasny\Entity\EntityInterface;
use Jasny\EntityCollection\EntityList;

/**
 * @covers \Jasny\EntityCollection\EntityList
 * @covers \Jasny\EntityCollection\AbstractEntityCollection
 */
class EntityListTest extends TestCase
{
    use \Jasny\TestHelper;

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
    public function setUp()
    {
        $this->entities = [
            $this->createMock(EntityInterface::class),
            $this->createMock(EntityInterface::class),
            $this->createMock(EntityInterface::class)
        ];

        $this->collection = (new EntityList(EntityInterface::class))
            ->withEntities($this->entities);
    }

    public function testCreate()
    {
        $entities = array_combine([0, 1, 27], $this->entities);

        $list = (new EntityList(EntityInterface::class))
            ->withEntities($entities);

        $this->assertSame($this->entities, iterator_to_array($list));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage NotExist does not implement Jasny\Entity\EntityInterface
     */
    public function testCreateInvalidClass()
    {
        new EntityList('NotExist');
    }

    public function testGetEntityClass()
    {
        $list = new EntityList(AbstractEntity::class);

        $this->assertEquals(AbstractEntity::class, $list->getEntityClass());
    }

    public function testAdd()
    {
        $newEntity = $this->createMock(EntityInterface::class);
        $this->collection->add($newEntity);

        $this->assertCount(4, $this->collection);
        $this->assertSame(array_merge($this->entities, [$newEntity]), iterator_to_array($this->collection));
    }

    public function testRemoveById()
    {
        $this->entities[0]->expects($this->once())->method('is')->with(42)->willReturn(false);
        $this->entities[1]->expects($this->once())->method('is')->with(42)->willReturn(true);
        $this->entities[2]->expects($this->once())->method('is')->with(42)->willReturn(false);

        $this->collection->remove(42);

        $this->assertCount(2, $this->collection);
        $this->assertSame([$this->entities[0], $this->entities[2]], iterator_to_array($this->collection));
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
        $this->assertSame([$this->entities[0], $this->entities[2]], iterator_to_array($this->collection));
    }

    public function testRemoveMultiple()
    {
        $this->entities[0]->expects($this->once())->method('is')->with(42)->willReturn(false);
        $this->entities[1]->expects($this->once())->method('is')->with(42)->willReturn(true);
        $this->entities[2]->expects($this->once())->method('is')->with(42)->willReturn(true);

        $this->collection->remove(42);

        $this->assertCount(1, $this->collection);
        $this->assertSame([$this->entities[0]], iterator_to_array($this->collection));
    }
}
