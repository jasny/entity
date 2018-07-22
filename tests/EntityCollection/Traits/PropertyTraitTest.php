<?php

namespace Jasny\EntityCollection;

use PHPUnit\Framework\TestCase;
use Jasny\EntityInterface;
use Jasny\EntityCollection\Traits\PropertyTrait;

/**
 * @covers Jasny\EntityCollection\Traits\PropertyTrait
 */
class PropertyTraitTest extends TestCase
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
        $this->collection = $this->getMockForTrait(PropertyTrait::class);
    }

    /**
     * Provide data for testing 'getAll' method
     *
     * @return array
     */
    public function getAllProvider()
    {
        $entity1 = (object)['foo' => 1];
        $entity2 = (object)['foo' => 2];
        $entity3 = (object)['foo' => null];
        $entity4 = (object)['foo' => 4];
        $entity5 = (object)['foo' => null];

        $entities = [$entity1, $entity2, $entity3, $entity4, $entity5];

        return [
            [$entities, true, [0 => 1, 1 => 2, 3 => 4]],
            [$entities, false, [1, 2, null, 4, null]],
            [[$entity3], true, []],
            [[$entity3], false, [0 => null]],
            [[$entity1], true, [1]],
            [[$entity1], false, [1]],
            [[], true, []],
            [[], false, []],
        ];
    }

    /**
     * Test 'getAll' method
     *
     * @dataProvider getAllProvider
     */
    public function testGetAll($entities, $skipNotSet, $expected)
    {
        $this->collection->entities = $entities;

        $iterator = $this->collection->getAll('foo', $skipNotSet);
        $result = iterator_to_array($iterator);

        $this->assertSame($expected, $result);
    }

    /**
     * Provide data for testing 'getAllById' method
     *
     * @return array
     */
    public function getAllByIdProvider()
    {
        $entity1 = $this->createConfiguredMock(EntityInterface::class, ['getId' => 'id1']);
        $entity2 = $this->createConfiguredMock(EntityInterface::class, ['getId' => 'id2']);
        $entity3 = $this->createConfiguredMock(EntityInterface::class, ['getId' => 'id3']);
        $entity4 = $this->createConfiguredMock(EntityInterface::class, ['getId' => 'id4']);
        $entity5 = $this->createConfiguredMock(EntityInterface::class, ['getId' => 'id5']);

        $entity1->foo = 1;
        $entity2->foo = 2;
        $entity3->foo = null;
        $entity4->foo = 4;
        $entity5->foo = null;

        $entities = [$entity1, $entity2, $entity3, $entity4, $entity5];

        return [
            [$entities, true, ['id1' => 1, 'id2' => 2, 'id4' => 4]],
            [$entities, false, ['id1' => 1, 'id2' => 2, 'id3' => null, 'id4' => 4, 'id5' => null]],
            [[$entity3], true, []],
            [[$entity3], false, ['id3' => null]],
            [[$entity1], true, ['id1' => 1]],
            [[$entity1], false, ['id1' => 1]],
            [[], true, []],
            [[], false, []],
        ];
    }

    /**
     * Test 'getAllById' method
     *
     * @dataProvider getAllByIdProvider
     */
    public function testGetAllById($entities, $skipNotSet, $expected)
    {
        $this->collection->entities = $entities;

        $iterator = $this->collection->getAllById('foo', $skipNotSet);
        $result = iterator_to_array($iterator);

        $this->assertSame($expected, $result);
    }

    /**
     * Provide data for testing 'getUnique' method
     *
     * @return array
     */
    public function getUniqueProvider()
    {
        $entity1 = (object)['foo' => 1];
        $entity2 = (object)['foo' => 2];
        $entity3 = (object)['foo' => null];
        $entity4 = (object)['foo' => 4];
        $entity5 = (object)['foo' => null];
        $entity6 = (object)['foo' => 2];
        $entity7 = (object)['foo' => 4];
        $entity8 = (object)['foo' => ['bar', 'baz']];
        $entity9 = (object)['foo' => ['bar', 'baz']];

        $entities = [$entity1, $entity2, $entity3, $entity4, $entity5, $entity6, $entity7];
        $entities2 = [$entity1, $entity2, $entity3, $entity4, $entity5, $entity6, $entity7, $entity8, $entity9];

        return [
            [$entities, false, [1, 2, 4]],
            [$entities, true, [1, 2, 4]],
            [$entities2, true, [1, 2, 4, 'bar', 'baz']]
        ];
    }

    /**
     * Test 'getUnique' method
     *
     * @dataProvider getUniqueProvider
     */
    public function testGetUnique($entities, $flatten, $expected)
    {
        $this->collection->entities = $entities;

        $result = $this->collection->getUnique('foo', $flatten);

        $this->assertSame($expected, $result);
    }

    /**
     * Provide data for testing 'sum' method
     *
     * @return array
     */
    public function sumProvider()
    {
        $entity1 = (object)['foo' => 1];
        $entity2 = (object)['foo' => 2];
        $entity3 = (object)['foo' => null];
        $entity4 = (object)['foo' => 5];
        $entity5 = (object)['foo' => -4];
        $entity6 = (object)['foo' => null];

        $entities = [$entity1, $entity2, $entity3, $entity4, $entity5, $entity6];

        return [
            [$entities, 4],
            [[], 0]
        ];
    }

    /**
     * Test 'sum' method
     *
     * @dataProvider sumProvider
     */
    public function testSum($entities, $expected)
    {
        $this->collection->entities = $entities;

        $result = $this->collection->sum('foo');

        $this->assertSame($expected, $result);
    }
}
