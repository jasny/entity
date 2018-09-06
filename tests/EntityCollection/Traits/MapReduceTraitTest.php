<?php

namespace Jasny\EntityCollection\Traits\Tests;

use PHPUnit\Framework\TestCase;
use Jasny\EntityInterface;
use Jasny\EntityCollection\Traits\MapReduceTrait;

/**
 * @covers Jasny\EntityCollection\Traits\MapReduceTrait
 */
class MapReduceTraitTest extends TestCase
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
        $this->collection = $this->getMockForTrait(MapReduceTrait::class);
    }

    /**
     * Provide data for testing 'map' method
     *
     * @return array
     */
    public function mapProvider()
    {
        return [
            [['foo', 'bar', 'baz'], ['foo0', 'bar1', 'baz2']],
            [[], []]
        ];
    }

    /**
     * Test 'map' method
     *
     * @dataProvider mapProvider
     */
    public function testMap($entities, $expected)
    {
        $callback = function($entity, $key) {
            return $entity . $key;
        };

        $this->collection->entities = $entities;
        $result = $this->collection->map($callback);

        $this->assertSame($expected, $result);
    }

    /**
     * Provide data for testing 'mapItems' method
     *
     * @return array
     */
    public function mapItemsProvider()
    {
        $entity1 = $this->createMock(EntityInterface::class);
        $entity3 = $this->createMock(EntityInterface::class);

        $entity1->foo = 'entity1';
        $entity3->foo = 'entity3';

        return [
            [
                ['id1' => 'item1', 'id2' => 'item2', 'id3' => 'item3'],
                [['id1', $entity1], ['id2' => null], ['id3', $entity3]],
                ['id1' => 'entity1-item1', 'id3' => 'entity3-item3']
            ],
            [
                ['id1' => 'item1', 'id2' => 'item2', 'id3' => 'item3'],
                [['id1', null], ['id2' => null], ['id3', null]],
                []
            ],
            [
                [], [], []
            ]
        ];
    }

    /**
     * Test 'mapItems' method
     *
     * @dataProvider mapItemsProvider
     */
    public function testMapItems($items, $mapEntities, $expected)
    {
        if ($mapEntities) {
            $this->collection->method('get')->will($this->returnValueMap($mapEntities));
        }

        $callback = function($entity, $item) {
            return $entity->foo . '-' . $item;
        };

        $result = $this->collection->mapItems($items, $callback);

        $this->assertSame($expected, $result);
    }

    /**
     * Provide data for testing 'reduce' method
     *
     * @return array
     */
    public function reduceProvider()
    {
        return [
            [[1, 2, 3], null, 'start-1-2-3'],
            [[1, 2, 3], 0, '0-1-2-3'],
            [[1], null, 'start-1'],
            [[1], 0, '0-1'],
            [[], null, null],
            [[], 'initial', 'initial']
        ];
    }

    /**
     * Test 'reduce' method
     *
     * @dataProvider reduceProvider
     */
    public function testReduce($entities, $initial, $expected)
    {
        $this->collection->entities = $entities;

        $callback = function($prevResult, $item) {
            return (isset($prevResult) ? $prevResult : 'start') . '-' . $item;
        };

        $result = $this->collection->reduce($callback, $initial);

        $this->assertSame($expected, $result);
    }
}
