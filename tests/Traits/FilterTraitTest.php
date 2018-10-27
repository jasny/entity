<?php

namespace Jasny\EntityCollection\Tests\Traits;

use Jasny\EntityCollection\EntitySet;
use Jasny\TestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Jasny\Entity\Entity;
use Jasny\EntityCollection\Traits\FilterTrait;
use function Jasny\array_only;

/**
 * @covers \Jasny\EntityCollection\Traits\FilterTrait
 */
class FilterTraitTest extends TestCase
{
    use TestHelper;

    /**
     * @var FilterTrait|MockObject
     */
    protected $collection;

    /**
     * @var Entity[]
     */
    protected $entities;

    protected function setUpEntities()
    {
        $entity1 = $this->createMock(Entity::class);
        $entity2 = $this->createMock(Entity::class);
        $entity3 = $this->createMock(Entity::class);
        $entity4 = $this->createMock(Entity::class);
        $entity5 = $this->createMock(Entity::class);
        $entity6 = $this->createMock(Entity::class);

        $entity1->foo = 'bar';
        $entity2->foo = 123;
        $entity4->foo = '123';
        $entity5->foo = ['1230'];
        $entity6->foo = [1234, 123, 'teta'];

        $this->entities = [$entity1, $entity2, $entity3, $entity4, $entity5, $entity6];
    }

    /**
     * Set up dependencies before each test
     */
    public function setUp()
    {
        $this->setUpEntities();

        $this->collection = $this->getMockBuilder(FilterTrait::class)
            ->setMethods(['getEntityClass', 'createEntitySet'])
            ->getMockForTrait();

        $this->collection->expects($this->any())->method('getEntityClass')->willReturn(Entity::class);

        $this->setPrivateProperty($this->collection, 'entities', $this->entities);
    }

    /**
     * Provide data for testing 'filter' method
     *
     * @return array
     */
    public function filterProvider()
    {
        return [
            [['foo' => 123], false, [1, 3, 5]],
            [['foo' => 123], true, [1, 5]],
            [[], false, [0, 1, 2, 3, 4, 5]],
            [['foo' => 'non_existing'], false, []]
        ];
    }

    /**
     * Test 'filter' method
     *
     * @dataProvider filterProvider
     */
    public function testFilter($filter, $strict, $only)
    {
        $newCollection = clone $this->collection;
        $expected = array_only($this->entities, $only);

        $this->collection->expects($this->once())->method('withEntities')
            ->with($this->identicalTo($expected))->willReturn($newCollection);

        $result = $this->collection->filter($filter, $strict);

        $this->assertSame($newCollection, $result);
    }

    public function testFilterCallback()
    {
        $filter = function($entity) {
            return isset($entity->foo) && is_string($entity->foo);
        };

        $this->collection->expects($this->once())->method('withEntities')
            ->with(array_only($this->entities, [0, 3]))->willReturnSelf();

        $this->collection->filter($filter);
    }

    public function testFilterCallbackWithKeys()
    {
        $filter = function($key) {
            return $key > 1 && $key <= 4;
        };

        $this->collection->expects($this->once())->method('withEntities')
            ->with(array_only($this->entities, [2, 3, 4]))->willReturnSelf();

        $this->collection->filter($filter, ARRAY_FILTER_USE_KEY);
    }

    public function testFilterCallbackWithPairs()
    {
        $filter = function($entity, $key) {
            return ($key > 1 && $key <= 4) || (isset($entity->foo) && is_string($entity->foo));
        };

        $this->collection->expects($this->once())->method('withEntities')
            ->with(array_only($this->entities, [0, 2, 3, 4]))->willReturnSelf();

        $this->collection->filter($filter, ARRAY_FILTER_USE_BOTH);
    }


    /**
     * Provide data for testing 'find' method
     *
     * @return array
     */
    public function findProvider()
    {
        return [
            [['foo' => '123'], false, 1],
            [['foo' => '123'], true, 3],
            [[], false, 0],
            [['foo' => 'non_existing'], false, null]
        ];
    }

    /**
     * Test 'find' method
     *
     * @dataProvider findProvider
     */
    public function testFind($filter, $strict, $index)
    {
        $expected = isset($index) ? $this->entities[$index] : null;
        $result = $this->collection->find($filter, $strict);

        $this->assertSame($expected, $result);
    }

    public function testFindCallback()
    {
        $filter = function($entity) {
            return isset($entity->foo) && is_array($entity->foo);
        };

        $result = $this->collection->find($filter);

        $this->assertSame($this->entities[4], $result);
    }

    public function testFindCallbackWithKeys()
    {
        $filter = function($key) {
            return $key > 1 && $key <= 4;
        };

        $result = $this->collection->find($filter, ARRAY_FILTER_USE_KEY);

        $this->assertSame($this->entities[2], $result);
    }

    public function testFindCallbackWithPairs()
    {
        $filter = function($entity, $key) {
            return $key > 0 && isset($entity->foo) && is_string($entity->foo);
        };

        $result = $this->collection->find($filter, ARRAY_FILTER_USE_BOTH);

        $this->assertSame($this->entities[3], $result);
    }
    
    
    /**
     * Test 'unique' method.
     * Making is unique is done by EntitySet, so the entities are simply passed.
     */
    public function testUnique()
    {
        $entitySet = $this->createMock(EntitySet::class);
        $entitySet->expects($this->once())->method('withEntities')->with($this->entities)->willReturnSelf();

        $this->collection->expects($this->once())->method('createEntitySet')->willReturn($entitySet);

        $result = $this->collection->unique();

        $this->assertNotSame($this->collection, $result);
        $this->assertSame($entitySet, $result);
    }
}
