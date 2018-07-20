<?php

namespace Jasny\EntityCollection;

use PHPUnit\Framework\TestCase;
use Jasny\Support\IdentifyTestEntity;
use Jasny\Entity;
use Jasny\EntityCollection\Traits\AssertTrait;

/**
 * @covers Jasny\EntityCollection\Traits\AssertTrait
 * @group collection
 */
class AssertTraitTest extends TestCase
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
        $this->collection = $this->getMockForTrait(AssertTrait::class);
    }

    /**
     * Test 'assertEntityClass' method
     */
    public function testAssertEntityClass()
    {
        $this->collection->entityClass = Entity::class;

        $this->callPrivateMethod($this->collection, 'assertEntityClass', []);

        $this->assertTrue(true); //We're ok if no exception was thrown on previous step
    }

    /**
     * Provide data for testing 'assertEntityClass' method
     *
     * @return array
     */
    public function assertEntityClassProvider()
    {
        return [
            [null],
            ['']
        ];
    }

    /**
     * Test 'assertEntityClass' method, in case when entityClass is empty
     *
     * @dataProvider assertEntityClassProvider
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage Entity class not set
     */
    public function testAssertEntityClassNoClassSet($entityClass)
    {
        $this->collection->entityClass = $entityClass;

        $this->callPrivateMethod($this->collection, 'assertEntityClass', []);
    }

    /**
     * Test 'assertEntityClass' method, in case when entity class does not represent an EntityInterface
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegExp /\w+ is not an Entity/
     */
    public function testAssertEntityClassNotEntity()
    {
        $this->collection->entityClass = 'Foo';

        $this->callPrivateMethod($this->collection, 'assertEntityClass', []);
    }

    /**
     * Test 'assertEntity' method
     */
    public function testAssertEntity()
    {
        $entity = $this->createMock(Entity::class);
        $this->collection->entityClass = Entity::class;

        $this->callPrivateMethod($this->collection, 'assertEntity', [$entity]);

        $this->assertTrue(true); //We're ok if no exception was thrown on previous step
    }

    /**
     * Test 'assertEntity' method, in case when parameter is not an instance of EntityInterface
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage string is not an Entity
     */
    public function testAssertEntityNotEntity()
    {
        $this->callPrivateMethod($this->collection, 'assertEntity', ['foo']);
    }

    /**
     * Test 'assertEntity' method, in case when parameter has wrong class
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegExp /\w+ is not a Foo entity/
     */
    public function testAssertEntityNotEntityClass()
    {
        $entity = $this->createMock(Entity::class);
        $this->collection->entityClass = 'Foo';

        $this->callPrivateMethod($this->collection, 'assertEntity', [$entity]);
    }

    /**
     * Provide data for testing 'assertIndex' method
     *
     * @return array
     */
    public function assertIndexProvider()
    {
        return [
            [[1, 2, 3], 0, false],
            [[1, 2, 3], 0, true],
            [[1, 2, 3], 1, false],
            [[1, 2, 3], 1, true],
            [[1, 2, 3], 2, false],
            [[1, 2, 3], 2, true],
            [[1, 2, 3], 3, true],
            [[], 0, true]
        ];
    }

    /**
     * Test 'assertIndex' method
     *
     * @dataProvider assertIndexProvider
     */
    public function testAssertIndex($entities, $index, $add)
    {
        $this->collection->entities = $entities;

        $this->callPrivateMethod($this->collection, 'assertIndex', [$index, $add]);

        $this->assertTrue(true); //We're ok if no exception was thrown on previous step
    }

    /**
     * Provide data for testing 'assertIndex' method, in case when index is not an integer
     *
     * @return array
     */
    public function assertIndexNotIntProvider()
    {
        return [
            ['12'],
            ['foo'],
            [1.2],
        ];
    }

    /**
     * Test 'assertIndex' method, in case when index is not an integer
     *
     * @dataProvider assertIndexNotIntProvider
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Only numeric keys are allowed
     */
    public function testAssertIndexNotInt($index)
    {
        $this->callPrivateMethod($this->collection, 'assertIndex', [$index]);
    }



    /**
     * Provide data for testing 'assertIndex' method, in case when index is out of bounds
     *
     * @return array
     */
    public function assertIndexOutOfBoundsProvider()
    {
        return [
            [[1, 2, 3], -1, true],
            [[1, 2, 3], -1, false],
            [[1, 2, 3], 4, true],
            [[1, 2, 3], 3, false],
            [[], 0, false]
        ];
    }

    /**
     * Test 'assertIndex' method, in case when index is out of bounds
     *
     * @dataProvider assertIndexOutOfBoundsProvider
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessageRegExp /Index '-?\d+' is out of bounds/
     */
    public function testAssertIndexOutOfBounds($entities, $index, $add)
    {
        $this->collection->entities = $entities;

        $this->callPrivateMethod($this->collection, 'assertIndex', [$index, $add]);
    }
}
