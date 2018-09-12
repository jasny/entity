<?php

namespace Jasny\EntityCollection\Tests\Traits;

use Jasny\TestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Jasny\Entity\EntityInterface;
use Jasny\EntityCollection\Traits\MapReduceTrait;

/**
 * @covers \Jasny\EntityCollection\Traits\MapReduceTrait
 */
class MapReduceTraitTest extends TestCase
{
    use TestHelper;

    /**
     * @var MapReduceTrait|MockObject
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
        $this->setPrivateProperty($this->collection, 'entities', $entities);

        $callback = function($entity, $key) {
            return $entity . $key;
        };

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
        return [
            [
                ['id1' => 'item1', 'id2' => 'item2', 'id3' => 'item3'],
                ['id1' => 'entity1-item1', 'id3' => 'entity3-item3']
            ],
            [
                ['no1' => 'item1', 'no2' => 'item2', 'no3' => 'item3'],
                []
            ],
            [
                [],
                []
            ]
        ];
    }

    /**
     * Test 'mapItems' method
     *
     * @dataProvider mapItemsProvider
     */
    public function testMapItems($items, $expected)
    {
        $entity1 = $this->createConfiguredMock(EntityInterface::class, ['getId' => 'id1']);
        $entity3 = $this->createConfiguredMock(EntityInterface::class, ['getId' => 'id3']);

        $entity1->foo = 'entity1';
        $entity3->foo = 'entity3';

        $this->setPrivateProperty($this->collection, 'entities', [$entity1, $entity3]);

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
        $this->setPrivateProperty($this->collection, 'entities', $entities);

        $callback = function($prevResult, $item) {
            return (isset($prevResult) ? $prevResult : 'start') . '-' . $item;
        };

        $result = $this->collection->reduce($callback, $initial);

        $this->assertSame($expected, $result);
    }
}
