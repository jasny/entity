<?php

namespace Jasny\EntityCollection\Traits\Tests;

use PHPUnit\Framework\TestCase;
use Jasny\EntityInterface;
use Jasny\EntityCollection\Traits\GetSetTrait;

/**
 * @covers Jasny\EntityCollection\Traits\GetSetTrait
 */
class GetSetTraitTest extends TestCase
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
        $this->collection = $this->getMockForTrait(GetSetTrait::class);
    }

    /**
     * Test 'add' method
     */
    public function testAdd()
    {
        $entity = $this->createMock(EntityInterface::class);
        $this->collection->expects($this->once())->method('offsetSet')->with(null, $entity);

        $this->collection->add($entity);
    }

    /**
     * Provide data for testing 'contains' method
     *
     * @return array
     */
    public function containsProvider()
    {
        $entity = $this->createMock(EntityInterface::class);

        return [
            [$entity, $entity, true],
            ['foo_entity_id', $entity, true],
            [$entity, null, false],
            ['foo_entity_id', null, false]
        ];
    }

    /**
     * Test 'contains' method
     *
     * @dataProvider containsProvider
     */
    public function testContains($entity, $fetched, $expected)
    {
        $collection = $this->getMockForTrait(GetSetTrait::class, [], '', true, true, true, ['get']);
        $collection->expects($this->once())->method('get')->with($entity)->willReturn($fetched);

        $result = $collection->contains($entity);

        $this->assertSame($expected, $result);
    }

    /**
     * Provide data for testing 'get' method
     *
     * @return array
     */
    public function getProvider()
    {
        $entity = $this->createMock(EntityInterface::class);

        return [
            [$entity, $entity],
            ['foo_entity_id', $entity],
            [$entity, $entity],
            ['foo_entity_id', $entity]
        ];
    }

    /**
     * Test 'get' method
     *
     * @dataProvider getProvider
     */
    public function testGet($search, $entity)
    {
        $iterator = $this->createMock(\Iterator::class);
        $iterator->expects($this->once())->method('current')->willReturn($entity);

        $collection = $this->getMockForTrait(GetSetTrait::class, [], '', true, true, true, ['findEntity']);
        $collection->expects($this->once())->method('findEntity')->with($search)->willReturn($iterator);

        $result = $collection->get($search);

        $this->assertSame($entity, $result);
    }

    /**
     * Provide data for testing 'remove' method
     *
     * @return array
     */
    public function removeProvider()
    {
        $entities = ['foo', 'bar', 'baz', 'zoo'];

        return [
            [$entities, 'some_search_id', [1 => 'bar', 3 => 'zoo'], ['foo', 'baz']],
            [$entities, $this->createMock(EntityInterface::class), [1 => 'bar', 3 => 'zoo'], ['foo', 'baz']],
            [[], 'some_search_id', [], []],
            [[], $this->createMock(EntityInterface::class), [], []]
        ];
    }

    /**
     * Test 'remove' method
     *
     * @dataProvider removeProvider
     */
    public function testRemove($entities, $search, $remove, $expected)
    {
        $this->collection->entities = $entities;
        $this->collection->expects($this->once())->method('findEntity')->with($search)->willReturn($remove);

        $this->collection->remove($search);

        $this->assertSame($expected, $this->collection->entities);
    }
}
