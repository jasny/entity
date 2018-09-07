<?php

namespace Jasny\Tests\EntityCollection;

use PHPUnit\Framework\TestCase;
use Jasny\Entity\Entity;
use Jasny\EntityCollection\EntitySet;
use Jasny\Tests\Support\TestEntity;

/**
 * @covers Jasny\EntityCollection\EntitySet
 */
class EntitySetTest extends TestCase
{
    use \Jasny\TestHelper;

    /**
     * Collection mock
     * @var EntitySet
     **/
    public $collection;

    /**
     * Class for mocking entity with id
     * @var string
     */
    public static $mockEntityClass;

    /**
     * Set up dependencies
     */
    public function setUp()
    {
        $this->collection = $this->createPartialMock(EntitySet::class, []);
    }

    /**
     * Test 'assertEntityClass' method
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegExp /[\w\\]+ is not an identifiable, can't create a set/
     */
    public function testAssertEntityClass()
    {
        $this->setPrivateProperty($this->collection, 'entityClass', Entity::class);
        $this->callPrivateMethod($this->collection, 'assertEntityClass', []);
    }

    /**
     * Test creating set
     */
    public function testCreate()
    {
        $class = static::getMockEntityClass();

        $entity1 = new $class('a');
        $entity2 = new $class('b');
        $entity3 = new $class('a');
        $entity4 = new $class('c');

        $entities = [$entity1, $entity2, $entity3, $entity4];

        $set = EntitySet::forClass($class, $entities);

        $this->assertAttributeSame([$entity1, $entity2, $entity4], 'entities', $set);
        $this->assertAttributeSame(['a' => $entity1, 'b' => $entity2, 'c' => $entity4], 'map', $set);
    }

    /**
     * Provide data for testing 'contains' method
     *
     * @return array
     */
    public function containsProvider()
    {
        $class = static::getMockEntityClass();

        $entity1 = new $class('a');
        $entity2 = new $class('d');

        return [
            ['a', true],
            [$entity1, true],
            ['d', false],
            [$entity2, false]
        ];
    }

    /**
     * Test 'contains' method
     *
     * @dataProvider containsProvider
     */
    public function testContains($search, $expected)
    {
        $set = $this->getSetMock();

        $result = $set->contains($search);
        $this->assertSame($expected, $result);
    }

    /**
     * Test 'unique' method
     */
    public function testUnique()
    {
        $set = $this->createPartialMock(EntitySet::class, []);

        $result = $set->unique();

        $this->assertSame($set, $result);
    }

    /**
     * Provide data for testing 'get' method
     *
     * @return array
     */
    public function getProvider()
    {
        return [
            ['b', 'b'],
            ['d', null],
            [3, null]
        ];
    }

    /**
     * Test 'get' method
     *
     * @dataProvider getProvider
     */
    public function testGet($search, $expected)
    {
        $set = $this->getSetMock();
        $result = $set->get($search);

        $expected ?
            $this->assertSame($expected, $result->getId()) :
            $this->assertSame($expected, $result);
    }

    /**
     * Provide data for testing 'remove' method
     *
     * @return array
     */
    public function removeProvider()
    {
        $class = static::getMockEntityClass();

        $entity1 = new $class('a');
        $entity2 = new $class('b');
        $entity3 = new $class('c');
        $entity4 = new $class('d');

        $map = ['a' => $entity1, 'b' => $entity2, 'c' => $entity3];
        $entities = [$entity1, $entity2, $entity3];

        return [
            ['b', $entities, $map, [$entity1, $entity3], ['a' => $entity1, 'c' => $entity3]],
            [$entity2, $entities, $map, [$entity1, $entity3], ['a' => $entity1, 'c' => $entity3]],
            ['d', $entities, $map, $entities, $map],
            [$entity4, $entities, $map, $entities, $map],
        ];
    }

    /**
     * Test 'remove' method
     *
     * @dataProvider removeProvider
     */
    public function testRemove($search, $entities, $map, $expectedEntities, $expectedMap)
    {
        $set = $this->createPartialMock(EntitySet::class, []);
        $this->setPrivateProperty($set, 'map', $map);
        $this->setPrivateProperty($set, 'entities', $entities);

        $set->remove($search);

        $this->assertAttributeSame($expectedMap, 'map', $set);
        $this->assertAttributeSame($expectedEntities, 'entities', $set);
    }

    /**
     * Provide data for testing 'offsetSet' method
     *
     * @return array
     */
    public function offsetSetProvider()
    {
        $class = static::getMockEntityClass();

        $entity1 = new $class('a');
        $entity2 = new $class('b');
        $entity3 = new $class('c');
        $entity4 = new $class('d');

        $map = ['a' => $entity1, 'b' => $entity2, 'c' => $entity3];
        $entities = [$entity1, $entity2, $entity3];

        $map2 = ['a' => $entity1, 'b' => $entity2, 'c' => $entity3, 'd' => $entity4];
        $entities2 = [$entity1, $entity2, $entity3, $entity4];

        return [
            [null, $entity1, $entities, $map, $entities, $map],
            [0, $entity1, $entities, $map, $entities, $map],
            [3, $entity1, $entities, $map, $entities, $map],
            [3, $entity4, $entities, $map, $entities2, $map2],
            [null, $entity4, $entities, $map, $entities2, $map2]
        ];
    }

    /**
     * Test 'offsetSet' method
     *
     * @dataProvider offsetSetProvider
     */
    public function testOffsetSet($index, $entity, $entities, $map, $expectedEntities, $expectedMap)
    {
        $class = static::getMockEntityClass();

        $set = $this->createPartialMock(EntitySet::class, []);
        $this->setPrivateProperty($set, 'map', $map);
        $this->setPrivateProperty($set, 'entities', $entities);
        $this->setPrivateProperty($set, 'entityClass', $class);

        $set->offsetSet($index, $entity);

        $this->assertAttributeSame($expectedMap, 'map', $set);
        $this->assertAttributeSame($expectedEntities, 'entities', $set);
    }

    /**
     * Test 'offsetSet' method, if entity at the index does not equal to the one we try to set
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage Can't replace entity in a set by index
     */
    public function testOffsetSetWrongIndex()
    {
        $class = static::getMockEntityClass();

        $entity1 = new $class('a');
        $entity2 = new $class('b');
        $entity3 = new $class('c');
        $entity4 = new $class('d');

        $map = ['a' => $entity1, 'b' => $entity2, 'c' => $entity3];
        $entities = [$entity1, $entity2, $entity3];

        $set = $this->createPartialMock(EntitySet::class, []);
        $this->setPrivateProperty($set, 'map', $map);
        $this->setPrivateProperty($set, 'entities', $entities);
        $this->setPrivateProperty($set, 'entityClass', $class);

        $set->offsetSet(1, $entity4);
    }

    /**
     * Provide data for testing 'offsetUnset' method
     *
     * @return array
     */
    public function offsetUnsetProvider()
    {
        $class = static::getMockEntityClass();

        $entity1 = new $class('a');
        $entity2 = new $class('b');
        $entity3 = new $class('c');

        $map = ['a' => $entity1, 'b' => $entity2, 'c' => $entity3];
        $entities = [$entity1, $entity2, $entity3];

        $map2 = ['a' => $entity1, 'c' => $entity3];
        $entities2 = [$entity1, $entity3];

        return [
            [1, $entities, $map, $entities2, $map2],
        ];
    }

    /**
     * Test 'offsetUnset' method
     *
     * @dataProvider offsetUnsetProvider
     */
    public function testOffsetUnset($index, $entities, $map, $expectedEntities, $expectedMap)
    {
        $set = $this->createPartialMock(EntitySet::class, []);
        $this->setPrivateProperty($set, 'map', $map);
        $this->setPrivateProperty($set, 'entities', $entities);

        $set->offsetUnset($index);

        $this->assertAttributeSame($expectedMap, 'map', $set);
        $this->assertAttributeSame($expectedEntities, 'entities', $set);
    }

    /**
     * Provide data for testing 'filter' method
     *
     * @return array
     */
    public function filterProvider()
    {
        $class = static::getMockEntityClass();

        $entity1 = new $class('a');
        $entity2 = new $class('b');
        $entity3 = new $class('c');
        $entity4 = new $class('d');
        $entity5 = new $class('e');
        $entity6 = new $class('f');

        $entity1->foo = 'bar';
        $entity2->foo = 123;
        $entity4->foo = '123';
        $entity5->foo = ['1230'];
        $entity6->foo = [1234, 123, 'teta'];

        $entities = [$entity1, $entity2, $entity3, $entity4, $entity5, $entity6];
        $map = ['a' => $entity1, 'b' => $entity2, 'c' => $entity3, 'd' => $entity4, 'e' => $entity5, 'f' => $entity6];

        $filter = function($entity) {
            return isset($entity->foo) && $entity->foo == 123;
        };

        return [
            [$entities, $map, ['foo' => 123], false, [$entity2, $entity4, $entity6], ['b' => $entity2, 'd' => $entity4, 'f' => $entity6]],
            [$entities, $map, ['foo' => 123], true, [$entity2, $entity6], ['b' => $entity2, 'f' => $entity6]],
            [$entities, $map, $filter, false, [$entity2, $entity4], ['b' => $entity2, 'd' => $entity4]],
            [$entities, $map, $filter, true, [$entity2, $entity4], ['b' => $entity2, 'd' => $entity4]],
            [$entities, $map, [], false, $entities, $map],
            [$entities, $map, [], true, $entities, $map],
            [[], [], ['foo' => 123], true, [], []],
            [[], [], ['foo' => 123], false, [], []],
            [[], [], $filter, true, [], []],
            [[], [], $filter, false, [], []],
        ];
    }

    /**
     * Test 'filter' method
     *
     * @dataProvider filterProvider
     */
    public function testFilter($entities, $map, $filter, $strict, $expectedEntities, $expectedMap)
    {
        $set = $this->createPartialMock(EntitySet::class, []);

        $this->setPrivateProperty($set, 'map', $map);
        $this->setPrivateProperty($set, 'entities', $entities);

        $result = $set->filter($filter, $strict);

        $this->assertSame(get_class($set), get_class($result));
        $this->assertNotSame($set, $result);
        $this->assertAttributeSame($expectedMap, 'map', $result);
        $this->assertAttributeSame($expectedEntities, 'entities', $result);
    }

    /**
     * Test 'sort' method
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage Set should not be used as ordered list
     */
    public function testSort()
    {
        $set = $this->createPartialMock(EntitySet::class, []);
        $set->sort();
    }

    /**
     * Test 'reverse' method
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage Set should not be used as ordered list
     */
    public function testReverse()
    {
        $set = $this->createPartialMock(EntitySet::class, []);
        $set->reverse();
    }

    /**
     * Get set mock
     *
     * @return EntitySet
     */
    protected function getSetMock()
    {
        $class = static::getMockEntityClass();

        $entity1 = new $class('a');
        $entity2 = new $class('b');
        $entity3 = new $class('c');

        $map = ['a' => $entity1, 'b' => $entity2, 'c' => $entity3];

        $set = $this->createPartialMock(EntitySet::class, []);
        $this->setPrivateProperty($set, 'map', $map);

        return $set;
    }

    /**
     * Get class for mocking entity with id
     * @return [type] [description]
     */
    protected static function getMockEntityClass()
    {
        if (!isset(static::$mockEntityClass)) {
            $source = new class() extends Entity {
                public $id;

                public function __construct($id = null)
                {
                    $this->id = $id;
                }
            };

            static::$mockEntityClass = get_class($source);
        }

        return static::$mockEntityClass;
    }
}
