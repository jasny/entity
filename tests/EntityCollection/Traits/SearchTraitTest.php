<?php

namespace Jasny\EntityCollection\Traits\Tests;

use PHPUnit\Framework\TestCase;
use Jasny\EntityInterface;
use Jasny\Support\IdentifyTestEntity;
use Jasny\EntityCollection\Traits\SearchTrait;

/**
 * @covers Jasny\EntityCollection\Traits\SearchTrait
 */
class SearchTraitTest extends TestCase
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
        $this->collection = $this->getMockForTrait(SearchTrait::class);
    }

    /**
     * Provide data for testing 'findEntity' method
     *
     * @return array
     */
    public function findEntityProvider()
    {
        return [
            [$this->createMock(EntityInterface::class), 'findEntityByRef'],
            ['bar_id', 'findEntityById']
        ];
    }

    /**
     * Test 'findEntity' method
     *
     * @dataProvider findEntityProvider
     */
    public function testFindEntity($entity, $method)
    {
        $expected = 'foo_result';

        $collection = $this->getMockForTrait(SearchTrait::class, [], '', true, true, true, [$method]);
        $collection->expects($this->once())->method($method)->with($entity)->willReturn($expected);

        $result = $this->callPrivateMethod($collection, 'findEntity', [$entity]);

        $this->assertSame($expected, $result);
    }

    /**
     * Provide data for testing 'findEntityByRef' method
     *
     * @return array
     */
    public function findEntityByRefProvider()
    {
        $entity1 = $this->createPartialMock(IdentifyTestEntity::class, ['getId']);
        $entity2 = $this->createPartialMock(IdentifyTestEntity::class, ['getId']);
        $entity3 = $this->createPartialMock(IdentifyTestEntity::class, ['getId']);
        $entity4 = $this->createPartialMock(IdentifyTestEntity::class, ['getId']);
        $entity5 = $this->createPartialMock(IdentifyTestEntity::class, ['getId']);
        $entity6 = $this->createPartialMock(IdentifyTestEntity::class, ['getId']);

        $entity1->method('getId')->willReturn('a');
        $entity2->method('getId')->willReturn('b');
        $entity3->method('getId')->willReturn('c');
        $entity4->method('getId')->willReturn('a');
        $entity5->method('getId')->willReturn('d');
        $entity6->method('getId')->willReturn('aa');

        $entities = [$entity1, $entity2, $entity3, $entity4, $entity5];

        return [
            [$entities, $entity1, [0 => $entity1, 3 => $entity4]],
            [$entities, $entity6, []],
            [[], $entity1, []]
        ];
    }

    /**
     * Test 'findEntityByRef' method
     *
     * @dataProvider findEntityByRefProvider
     */
    public function testFindEntityByRef($entities, $entity, $expected)
    {
        $this->collection->entities = $entities;

        $result = $this->callPrivateMethod($this->collection, 'findEntityByRef', [$entity]);
        $result = iterator_to_array($result);

        $this->assertSame($expected, $result);
    }

    /**
     * Provide data for testing 'findEntityById' method
     *
     * @return array
     */
    public function findEntityByIdProvider()
    {
        $entity1 = $this->createPartialMock(IdentifyTestEntity::class, ['getId']);
        $entity2 = $this->createPartialMock(IdentifyTestEntity::class, ['getId']);
        $entity3 = $this->createPartialMock(IdentifyTestEntity::class, ['getId']);
        $entity4 = $this->createPartialMock(IdentifyTestEntity::class, ['getId']);
        $entity5 = $this->createPartialMock(IdentifyTestEntity::class, ['getId']);
        $entity6 = $this->createPartialMock(IdentifyTestEntity::class, ['getId']);

        $entity1->method('getId')->willReturn('a');
        $entity2->method('getId')->willReturn('b');
        $entity3->method('getId')->willReturn('c');
        $entity4->method('getId')->willReturn('a');
        $entity5->method('getId')->willReturn('d');
        $entity6->method('getId')->willReturn('aa');

        $entities = [$entity1, $entity2, $entity3, $entity4, $entity5];

        return [
            [$entities, 'a', [0 => $entity1, 3 => $entity4]],
            [$entities, 'aa', []],
            [[], 'a', []]
        ];
    }

    /**
     * Test 'findEntityById' method
     *
     * @dataProvider findEntityByIdProvider
     */
    public function testFindEntityById($entities, $id, $expected)
    {
        $this->collection->entities = $entities;

        $result = $this->callPrivateMethod($this->collection, 'findEntityById', [$id]);
        $result = iterator_to_array($result);

        $this->assertSame($expected, $result);
    }
}
