<?php

namespace Jasny\EntityCollection;

use PHPUnit\Framework\TestCase;
use Jasny\EntityInterface;
use Jasny\Support\DynamicTestEntity;
use Jasny\Support\TestEntity;
use Jasny\EntityCollection\Traits\SortTrait;

/**
 * @covers Jasny\EntityCollection\Traits\SortTrait
 */
class SortTraitTest extends TestCase
{
    use \Jasny\TestHelper;

    /**
     * Collection trait mock
     **/
    public $collection;

    /**
     * Set up dependencies before each test
     */
    public function setUp()
    {
        $this->collection = $this->getMockForTrait(SortTrait::class);
    }

    /**
     * Provide data for testing 'sort' method
     *
     * @return array
     */
    public function sortProvider()
    {
        $entity1 = $this->createPartialMock(DynamicTestEntity::class, []);
        $entity2 = $this->createPartialMock(DynamicTestEntity::class, []);
        $entity3 = $this->createPartialMock(DynamicTestEntity::class, []);
        $entity4 = $this->createPartialMock(DynamicTestEntity::class, []);

        $entity1->foo = '2';
        $entity2->foo = '12';
        $entity3->foo = 'test2';
        $entity4->foo = 'test12';

        $entity5 = $this->createPartialMock(TestEntity::class, []);
        $entity6 = $this->createPartialMock(TestEntity::class, []);
        $entity7 = $this->createPartialMock(TestEntity::class, []);
        $entity8 = $this->createPartialMock(TestEntity::class, []);

        $entity5->foo = '2';
        $entity6->foo = '12';
        $entity7->foo = 'test2';
        $entity8->foo = 'test12';

        $entities1 = [$entity1, $entity2, $entity3, $entity4];
        $entities2 = [$entity5, $entity6, $entity7, $entity8];

        return [
            [$entities1, DynamicTestEntity::class, 'foo', SORT_REGULAR, [$entity1, $entity2, $entity4, $entity3]],
            [$entities1, DynamicTestEntity::class, 'foo', SORT_STRING, [$entity2, $entity1, $entity4, $entity3]],
            [$entities1, DynamicTestEntity::class, 'foo', SORT_NUMERIC, [$entity3, $entity4, $entity1, $entity2]],
            [$entities1, DynamicTestEntity::class, 'foo', SORT_NATURAL, [$entity1, $entity2, $entity3, $entity4]],
            [$entities1, DynamicTestEntity::class, null, SORT_REGULAR, [$entity1, $entity2, $entity4, $entity3]],
            [$entities1, DynamicTestEntity::class, null, SORT_STRING, [$entity2, $entity1, $entity4, $entity3]],
            [$entities1, DynamicTestEntity::class, null, SORT_NUMERIC, [$entity3, $entity4, $entity1, $entity2]],
            [$entities1, DynamicTestEntity::class, null, SORT_NATURAL, [$entity1, $entity2, $entity3, $entity4]],
            [$entities2, TestEntity::class, 'foo', SORT_REGULAR, [$entity5, $entity6, $entity8, $entity7]],
            [$entities2, TestEntity::class, 'foo', SORT_STRING, [$entity6, $entity5, $entity8, $entity7]],
            [$entities2, TestEntity::class, 'foo', SORT_NUMERIC, [$entity7, $entity8, $entity5, $entity6]],
            [$entities2, TestEntity::class, 'foo', SORT_NATURAL, [$entity5, $entity6, $entity7, $entity8]]
        ];
    }

    /**
     * Test 'sort' method
     *
     * @dataProvider sortProvider
     */
    public function testSort($entities, $entityClass, $property, $sortFlags, $expected)
    {
        $this->collection->entities = $entities;
        $this->collection->entityClass = $entityClass;

        $result = $this->collection->sort($property, $sortFlags);

        $this->assertSame($this->collection, $result);
        $this->assertSame($expected, $this->collection->entities);
    }

    /**
     * Test 'sort' method, in case when $property param is empty and __toString() method is not implemented
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessageRegExp /Class [\w\\]+ does not have __toString method, to use it for sorting/
     */
    public function testSortException()
    {
        $this->collection->entities = [];
        $this->collection->entityClass = TestEntity::class;

        $this->collection->sort();
    }
}
