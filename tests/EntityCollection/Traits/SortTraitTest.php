<?php

namespace Jasny\Tests\EntityCollection\Traits;

use PHPUnit\Framework\TestCase;
use Jasny\Entity\EntityInterface;
use Jasny\Tests\Support\TestEntity;
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
        $source = new class() {
            use SortTrait;

            public $foo;

            public function __toString()
            {
                return $this->foo;
            }
        };

        $class = get_class($source);

        $entity1 = new $class();
        $entity2 = new $class();
        $entity3 = new $class();
        $entity4 = new $class();

        $entity1->foo = '2';
        $entity2->foo = '12';
        $entity3->foo = 'test2';
        $entity4->foo = 'test12';

        $entities = [$entity1, $entity2, $entity3, $entity4];

        return [
            [$entities, $class, 'foo', SORT_REGULAR, [$entity1, $entity2, $entity4, $entity3]],
            [$entities, $class, 'foo', SORT_STRING, [$entity2, $entity1, $entity4, $entity3]],
            [$entities, $class, 'foo', SORT_NUMERIC, [$entity3, $entity4, $entity1, $entity2]],
            [$entities, $class, 'foo', SORT_NATURAL, [$entity1, $entity2, $entity3, $entity4]],
            [$entities, $class, null, SORT_REGULAR, [$entity1, $entity2, $entity4, $entity3]],
            [$entities, $class, null, SORT_STRING, [$entity2, $entity1, $entity4, $entity3]],
            [$entities, $class, null, SORT_NUMERIC, [$entity3, $entity4, $entity1, $entity2]],
            [$entities, $class, null, SORT_NATURAL, [$entity1, $entity2, $entity3, $entity4]]
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
