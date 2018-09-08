<?php

namespace Jasny\Tests\EntityCollection\Traits;

use PHPUnit\Framework\TestCase;
use Jasny\Entity\Entity;
use Jasny\Entity\EntityInterface;
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
        $class = static::getMockEntityClass();

        $entity1 = new $class('a');
        $entity2 = new $class('b');
        $entity3 = new $class('c');
        $entity4 = new $class('a');
        $entity5 = new $class('d');
        $entity6 = new $class('aa');

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
        $class = static::getMockEntityClass();

        $entity1 = new $class('a');
        $entity2 = new $class('b');
        $entity3 = new $class('c');
        $entity4 = new $class('a');
        $entity5 = new $class('d');
        $entity6 = new $class('aa');

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

    /**
     * Get class for mocking entity with id
     * @return [type] [description]
     */
    protected static function getMockEntityClass()
    {
        $source = new class() extends Entity {
            public $id;

            public function __construct($id = null)
            {
                $this->id = $id;
            }
        };

        return get_class($source);
    }
}
