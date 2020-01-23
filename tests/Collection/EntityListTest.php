<?php

namespace Jasny\Entity\Tests\Collection;

use Jasny\Entity\AbstractEntity;
use Jasny\Entity\Collection\EntityList;
use Jasny\Entity\EntityInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @covers \Jasny\Entity\Collection\EntityList
 * @covers \Jasny\Entity\Collection\AbstractCollection
 */
class EntityListTest extends TestCase
{
    /**
     * @var EntityList
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

        $this->collection = (new EntityList())->withEntities($this->entities);
    }

    public function testCreate()
    {
        $entities = array_combine([0, 1, 27], $this->entities);

        $list = (new EntityList())->withEntities($entities);

        $this->assertEquals(EntityInterface::class, $list->getType());
        $this->assertSame($this->entities, iterator_to_array($list));
    }

    public function testCreateWithChildClass()
    {
        $entities = [
            $this->createMock(AbstractEntity::class),
            $this->createMock(AbstractEntity::class),
            $this->createMock(AbstractEntity::class)
        ];

        $list = (new EntityList(AbstractEntity::class))->withEntities($entities);

        $this->assertEquals(AbstractEntity::class, $list->getType());
        $this->assertSame($entities, iterator_to_array($list));
    }

    public function testCreateWithInvalidClass()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("NotExist does not implement Jasny\Entity\EntityInterface");

        new EntityList('NotExist');
    }

    public function testCreateWithInvalidEntities()
    {
        $class = get_class($this->entities[0]);

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage("Expected instance of Jasny\Entity\AbstractEntity, instance of $class given");

        (new EntityList(AbstractEntity::class))->withEntities($this->entities);
    }

    public function testWithType()
    {
        $entities = [
            $this->createMock(AbstractEntity::class),
            $this->createMock(AbstractEntity::class),
            $this->createMock(AbstractEntity::class)
        ];

        $list = (new EntityList(EntityInterface::class))->withEntities($entities);
        $typedList = $list->withType(AbstractEntity::class);

        $this->assertNotSame($list, $typedList);
        $this->assertEquals(EntityInterface::class, $list->getType());
        $this->assertEquals(AbstractEntity::class, $typedList->getType());

        $this->assertSame($list->toArray(), $typedList->toArray());
    }

    public function testWithTypeWithInvalidEntities()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Not all entities are of type " . AbstractEntity::class);

        $this->collection->withType(AbstractEntity::class);
    }

    public function testWithTypeWithSameType()
    {
        $this->assertSame($this->collection, $this->collection->withType(EntityInterface::class));
    }

    public function testWithTypeBroaderType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(EntityInterface::class . " does not implement " . AbstractEntity::class);

        $typedList = new EntityList(AbstractEntity::class);
        $typedList->withType(EntityInterface::class);
    }


    public function testToArray()
    {
        $this->assertSame($this->entities, $this->collection->toArray());
    }

    public function testCount()
    {
        $this->assertSame(3, count($this->collection));
    }

    public function testIterate()
    {
        $result = [];

        foreach ($this->collection as $key => $entity) {
            $result[$key] = $entity;
        }

        $this->assertSame($this->entities, $result);
    }


    public function testAdd()
    {
        $newEntityInterface = $this->createMock(EntityInterface::class);
        $this->collection->add($newEntityInterface);

        $this->assertCount(4, $this->collection);
        $this->assertSame(array_merge($this->entities, [$newEntityInterface]), $this->collection->toArray());
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


    public function testContainsById()
    {
        $this->entities[0]->expects($this->once())->method('is')->with(42)->willReturn(false);
        $this->entities[1]->expects($this->once())->method('is')->with(42)->willReturn(true);
        $this->entities[2]->expects($this->never())->method('is');

        $this->assertTrue($this->collection->contains(42));
    }

    public function testContainsByRef()
    {
        $find = $this->entities[1];

        $this->entities[0]->expects($this->once())->method('is')->with($this->identicalTo($find))->willReturn(false);
        $this->entities[1]->expects($this->once())->method('is')->with($this->identicalTo($find))->willReturn(true);
        $this->entities[2]->expects($this->never())->method('is');

        $this->assertTrue($this->collection->contains($find));
    }

    public function testNotContainsById()
    {
        $this->entities[0]->expects($this->once())->method('is')->with(9)->willReturn(false);
        $this->entities[1]->expects($this->once())->method('is')->with(9)->willReturn(false);
        $this->entities[2]->expects($this->once())->method('is')->with(9)->willReturn(false);

        $this->assertFalse($this->collection->contains(9));
    }

    public function testNotContainsByRef()
    {
        $find = $this->createMock(EntityInterface::class);

        $this->entities[0]->expects($this->once())->method('is')->with($this->identicalTo($find))->willReturn(false);
        $this->entities[1]->expects($this->once())->method('is')->with($this->identicalTo($find))->willReturn(false);
        $this->entities[2]->expects($this->once())->method('is')->with($this->identicalTo($find))->willReturn(false);

        $this->assertFalse($this->collection->contains($find));
    }


    public function testSerialize()
    {
        $this->assertSame($this->entities, $this->collection->__serialize());
    }

    public function testUnserializeWithEntities()
    {
        $list = new EntityList();
        $list->__unserialize($this->entities);

        $this->assertEquals(EntityInterface::class, $list->getType());
        $this->assertSame($this->entities, $this->collection->toArray());
    }

    public function testUnserializeWithInvalidEntities()
    {
        $type = AbstractEntity::class;
        $class = get_class($this->entities[0]);

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage("Expected array of $type objects, got instance of $class");

        $list = new EntityList(AbstractEntity::class);
        $list->__unserialize($this->entities);
    }

    public function testUnserializeWithSubClass()
    {
        $entities = [
            $this->createMock(AbstractEntity::class),
            $this->createMock(AbstractEntity::class),
            $this->createMock(AbstractEntity::class)
        ];

        $sub = new class () extends EntityList
        {
            public function __construct()
            {
                parent::__construct(AbstractEntity::class);
            }
        };

        $list = (new ReflectionClass($sub))->newInstanceWithoutConstructor();
        $list->__unserialize($entities);

        $this->assertInstanceOf(get_class($sub), $list);
        $this->assertEquals(AbstractEntity::class, $list->getType());
        $this->assertSame($entities, $list->toArray());
    }

    public function testSetState()
    {
        $list = EntityList::__set_state($this->entities);

        $this->assertInstanceOf(EntityList::class, $list);
        $this->assertEquals(EntityInterface::class, $list->getType());
        $this->assertSame($this->entities, $list->toArray());
    }

    public function testSetStateWithSubClass()
    {
        $entities = [
            $this->createMock(AbstractEntity::class),
            $this->createMock(AbstractEntity::class),
            $this->createMock(AbstractEntity::class)
        ];

        $sub = new class () extends EntityList
        {
            public function __construct()
            {
                parent::__construct(AbstractEntity::class);
            }
        };

        $list = $sub::__set_state($entities);

        $this->assertInstanceOf(get_class($sub), $list);
        $this->assertEquals(AbstractEntity::class, $list->getType());
        $this->assertSame($entities, $list->toArray());
    }


    public function testJsonSerialize()
    {
        $this->assertSame($this->entities, $this->collection->jsonSerialize());
    }
}
