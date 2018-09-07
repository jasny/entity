<?php

namespace Jasny\Tests\EntityCollection\Traits;

use PHPUnit\Framework\TestCase;
use Jasny\Entity\EntityInterface;
use Jasny\EntityCollection\Traits\FilterTrait;

/**
 * @covers Jasny\EntityCollection\Traits\FilterTrait
 */
class FilterTraitTest extends TestCase
{
    /**
     * Collection trait mock
     **/
    public $collection;

    /**
     * Set up dependencies before each test
     */
    public function setUp()
    {
        $this->collection = $this->getMockForTrait(FilterTrait::class);
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
            [$entities, ['foo' => 123], false, [$entity2, $entity4, $entity6]],
            [$entities, ['foo' => 123], true, [$entity2, $entity6]],
            [$entities, $filter, false, [$entity2, $entity4]],
            [$entities, $filter, true, [$entity2, $entity4]],
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
        $this->collection->entities = $entities;

        $result = $this->collection->filter($filter, $strict);

        $this->assertSame(get_class($this->collection), get_class($result));
        $this->assertNotSame($this->collection, $result);
        $this->assertSame($expected, $result->entities);
    }

    /**
     * Provide data for testing 'unique' method
     *
     * @return array
     */
    public function uniqueProvider()
    {
        $entity1 = $this->createConfiguredMock(EntityInterface::class, ['getId' => 'a']);
        $entity2 = $this->createConfiguredMock(EntityInterface::class, ['getId' => 'a']);
        $entity3 = $this->createConfiguredMock(EntityInterface::class, ['getId' => 'b']);
        $entity4 = $this->createConfiguredMock(EntityInterface::class, ['getId' => 'c']);
        $entity5 = $this->createConfiguredMock(EntityInterface::class, ['getId' => 'a']);
        $entity6 = $this->createConfiguredMock(EntityInterface::class, ['getId' => 'c']);
        $entity7 = $this->createConfiguredMock(EntityInterface::class, ['getId' => 'd']);

        $entities = [$entity1, $entity2, $entity3, $entity4, $entity5, $entity6, $entity7];

        return [
            [$entities, [$entity1, $entity3, $entity4, $entity7]],
            [[], []]
        ];
    }

    /**
     * Test 'unique' method
     *
     * @dataProvider uniqueProvider
     */
    public function testUnique($entities, $expected)
    {
        $this->collection->entities = $entities;

        $result = $this->collection->unique();

        $this->assertSame(get_class($this->collection), get_class($result));
        $this->assertNotSame($this->collection, $result);
        $this->assertSame($expected, $result->entities);
    }
}
