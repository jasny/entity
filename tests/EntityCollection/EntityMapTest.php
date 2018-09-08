<?php

namespace Jasny\Tests\EntityCollection;

use PHPUnit\Framework\TestCase;
use Jasny\EntityCollection\EntityMap;
use Jasny\Entity\Entity;
use Jasny\Entity\EntityInterface;
use Jasny\Entity\Traits\GetSetTrait;
use Jasny\Entity\Traits\IdentifyTrait;
use Jasny\Entity\Traits\JsonSerializeTrait;
use Jasny\Entity\Traits\LazyLoadingTrait;
use Jasny\Entity\Traits\SetStateTrait;
use Jasny\Entity\Traits\TriggerTrait;

/**
 * @covers Jasny\EntityCollection\EntityMap
 */
class EntityMapTest extends TestCase
{
    use \Jasny\TestHelper;

    /**
     * Collection mock
     * @var EntitySet
     **/
    public $collection;

    /**
     * Set up dependencies
     */
    public function setUp()
    {
        $this->collection = $this->createPartialMock(EntityMap::class, []);
    }

    /**
     * Test 'sort' method
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage Map should not be used as ordered list
     */
    public function testSort()
    {
        $this->collection->sort();
    }

    /**
     * Test 'reverse' method
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage Map should not be used as ordered list
     */
    public function testReverse()
    {
        $this->collection->reverse();
    }

    /**
     * Provide data for testing 'offsetExists' method
     *
     * @return array
     */
    public function offsetExistsProvider()
    {
        return [
            [1, true],
            [3, true],
            [7, true],
            [0, false],
            [2, false]
        ];
    }

    /**
     * Test 'offsetExists' method
     *
     * @dataProvider offsetExistsProvider
     */
    public function testOffsetExists($index, $expected)
    {
        $collection = $this->getMapMock();
        $result = $collection->offsetExists($index);

        $this->assertSame($expected, $result);
    }

    /**
     * Provide data for testing 'offsetGet' method
     *
     * @return array
     */
    public function offsetGetProvider()
    {
        $entity1 = $this->createMock(EntityInterface::class);
        $entity2 = $this->createMock(EntityInterface::class);
        $entity3 = $this->createMock(EntityInterface::class);

        $entities = [1 => $entity1, 3 => $entity2, 7 => $entity3];

        return [
            [$entities, 1, $entity1],
            [$entities, 3, $entity2],
            [$entities, 7, $entity3]
        ];
    }

    /**
     * Test 'offsetGet' method
     *
     * @dataProvider offsetGetProvider
     */
    public function testOffsetGet($entities, $index, $expected)
    {
        $this->setPrivateProperty($this->collection, 'entities', $entities);

        $result = $this->collection->offsetGet($index);

        $this->assertSame($expected, $result);
    }

    /**
     * Test 'offsetGet' method, in case when index does not exists in collection
     *
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessage Key 2 does not exist in map
     */
    public function testOffsetGetIndexNotExists()
    {
        $entity1 = $this->createMock(EntityInterface::class);
        $entity2 = $this->createMock(EntityInterface::class);
        $entity3 = $this->createMock(EntityInterface::class);

        $entities = [1 => $entity1, 3 => $entity2, 7 => $entity3];

        $this->setPrivateProperty($this->collection, 'entities', $entities);

        $result = $this->collection->offsetGet(2);
    }

    /**
     * Provide data for testing 'offsetSet' method
     *
     * @return array
     */
    public function offsetSetProvider()
    {
        $entity1 = $this->createMock(EntityInterface::class);
        $entity2 = $this->createMock(EntityInterface::class);
        $entity3 = $this->createMock(EntityInterface::class);
        $entity4 = $this->createMock(EntityInterface::class);

        $entities = [1 => $entity1, 3 => $entity2, 7 => $entity3];

        return [
            [$entities, 0, $entity4, [1 => $entity1, 3 => $entity2, 7 => $entity3, 0 => $entity4]],
            [$entities, 0, $entity1, [1 => $entity1, 3 => $entity2, 7 => $entity3, 0 => $entity1]],
            [$entities, 1, $entity4, [1 => $entity4, 3 => $entity2, 7 => $entity3]],
            [$entities, 1, $entity1, [1 => $entity1, 3 => $entity2, 7 => $entity3]],
            [$entities, 2, $entity4, [1 => $entity1, 3 => $entity2, 7 => $entity3, 2 => $entity4]],
            [$entities, 2, $entity1, [1 => $entity1, 3 => $entity2, 7 => $entity3, 2 => $entity1]],
            [$entities, 10, $entity4, [1 => $entity1, 3 => $entity2, 7 => $entity3, 10 => $entity4]],
            [$entities, 10, $entity2, [1 => $entity1, 3 => $entity2, 7 => $entity3, 10 => $entity2]]
        ];
    }

    /**
     * Test 'offsetSet' method
     *
     * @dataProvider offsetSetProvider
     */
    public function testOffsetSet($entities, $index, $entity, $expected)
    {
        $this->setPrivateProperty($this->collection, 'entities', $entities);
        $this->setPrivateProperty($this->collection, 'entityClass', EntityInterface::class);

        $this->collection->offsetSet($index, $entity);

        $this->assertAttributeSame($expected, 'entities', $this->collection);
    }

    /**
     * Test 'offsetSet' method, in case when index is null
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Only numeric keys are allowed
     */
    public function testOffsetSetNullIndex()
    {
        $this->setPrivateProperty($this->collection, 'entityClass', EntityInterface::class);

        $entity = $this->createMock(EntityInterface::class);
        $this->collection->offsetSet(null, $entity);
    }

    /**
     * Test 'offsetUnset' method
     */
    public function testOffsetUnset()
    {
        $entity1 = $this->createMock(EntityInterface::class);
        $entity2 = $this->createMock(EntityInterface::class);
        $entity3 = $this->createMock(EntityInterface::class);

        $entities = [1 => $entity1, 3 => $entity2, 7 => $entity3];

        $this->setPrivateProperty($this->collection, 'entities', $entities);

        $this->collection->offsetUnset(3);

        $this->assertAttributeSame([1 => $entity1, 7 => $entity3], 'entities', $this->collection);
    }

    /**
     * Test 'offsetUnset' method, in case when index does not exists in collection
     *
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessage Key 0 does not exist in map
     */
    public function testOffsetUnsetIndexNotExists()
    {
        $entity1 = $this->createMock(EntityInterface::class);
        $entity2 = $this->createMock(EntityInterface::class);
        $entity3 = $this->createMock(EntityInterface::class);

        $entities = [1 => $entity1, 3 => $entity2, 7 => $entity3];

        $this->setPrivateProperty($this->collection, 'entities', $entities);

        $this->collection->offsetUnset(0);
    }

    /**
     * Provide data for testing 'remove' method
     *
     * @return array
     */
    public function removeProvider()
    {
        $source = new class() extends Entity {
            public $id;

            public function __construct($id = null)
            {
                $this->id = $id;
            }
        };

        $class = get_class($source);

        $entity1 = new $class('a');
        $entity2 = new $class('b');
        $entity3 = new $class('c');
        $entity4 = new $class('d');

        $entities = [1 => $entity1, 3 => $entity2, 7 => $entity3, 9 => $entity2];

        return [
            [$entities, $entity1, [3 => $entity2, 7 => $entity3, 9 => $entity2]],
            [$entities, 'a', [3 => $entity2, 7 => $entity3, 9 => $entity2]],
            [$entities, $entity2, [1 => $entity1, 7 => $entity3]],
            [$entities, 'b', [1 => $entity1, 7 => $entity3]],
            [$entities, 'd', $entities],
            [$entities, $entity4, $entities],
        ];
    }

    /**
     * Test 'remove' method
     *
     * @dataProvider removeProvider
     */
    public function testRemove($entities, $search, $expected)
    {
        $this->setPrivateProperty($this->collection, 'entities', $entities);

        $this->collection->remove($search);

        $this->assertAttributeSame($expected, 'entities', $this->collection);
    }

    /**
     * Test creating map
     */
    public function testCreate()
    {
        $entity1 = $this->createMock(EntityInterface::class);
        $entity2 = $this->createMock(EntityInterface::class);
        $entity3 = $this->createMock(EntityInterface::class);

        $entities = [1 => $entity1, 3 => $entity2, 7 => $entity3];

        $map = EntityMap::forClass(EntityInterface::class, $entities);

        $this->assertAttributeSame($entities, 'entities', $map);
    }

    /**
     * Provide data for testing 'filter' method
     *
     * @return array
     */
    public function filterProvider()
    {
        $entity1 = $this->createMock(EntityInterface::class);
        $entity2 = $this->createMock(EntityInterface::class);
        $entity3 = $this->createMock(EntityInterface::class);
        $entity4 = $this->createMock(EntityInterface::class);
        $entity5 = $this->createMock(EntityInterface::class);
        $entity6 = $this->createMock(EntityInterface::class);

        $entity1->foo = 'bar';
        $entity2->foo = 123;
        $entity4->foo = '123';
        $entity5->foo = ['1230'];
        $entity6->foo = [1234, 123, 'teta'];

        $entities = [$entity1, $entity2, $entity3, $entity4, $entity5, $entity6];

        $filter = function($entity) {
            return isset($entity->foo) && $entity->foo == 123;
        };

        return [
            [$entities, ['foo' => 123], false, [1 => $entity2, 3 => $entity4, 5 => $entity6]],
            [$entities, ['foo' => 123], true, [1 => $entity2, 5 => $entity6]],
            [$entities, $filter, false, [1 => $entity2, 3 => $entity4]],
            [$entities, $filter, true, [1 => $entity2, 3 => $entity4]],
            [$entities, [], false, $entities],
            [$entities, [], true, $entities],
            [[], ['foo' => 123], true, []],
            [[], ['foo' => 123], false, []],
            [[], $filter, true, []],
            [[], $filter, false, []],
        ];
    }

    /**
     * Test 'filter' method
     *
     * @dataProvider filterProvider
     */
    public function testFilter($entities, $filter, $strict, $expected)
    {
        $this->setPrivateProperty($this->collection, 'entities', $entities);

        $result = $this->collection->filter($filter, $strict);

        $this->assertSame(get_class($this->collection), get_class($result));
        $this->assertNotSame($this->collection, $result);
        $this->assertAttributeSame($expected, 'entities', $result);
    }

    /**
     * Get set mock
     *
     * @return EntityMap
     */
    protected function getMapMock()
    {
        $entity1 = $this->createMock(EntityInterface::class);
        $entity2 = $this->createMock(EntityInterface::class);
        $entity3 = $this->createMock(EntityInterface::class);

        $entities = [1 => $entity1, 3 => $entity2, 7 => $entity3];

        $set = $this->createPartialMock(EntityMap::class, []);
        $this->setPrivateProperty($set, 'entities', $entities);

        return $set;
    }
}
