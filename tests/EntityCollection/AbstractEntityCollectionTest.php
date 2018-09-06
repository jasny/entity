<?php

namespace Jasny\EntityCollection\Tests;

use PHPUnit\Framework\TestCase;
use Jasny\EntityCollection\AbstractEntityCollection;

/**
 * @covers Jasny\EntityCollection\AbstractEntityCollection
 */
class AbstractEntityCollectionTest extends TestCase
{
    use \Jasny\TestHelper;

    /**
     * Test 'getEntityClass' method
     */
    public function testGetEntityClass()
    {
        $class = AbstractEntityCollection::class;
        $collection = (new \ReflectionClass($class))->newInstanceWithoutConstructor();

        $this->setPrivateProperty($collection, 'entityClass', 'FooEntity');

        $result = $collection->getEntityClass();

        $this->assertSame('FooEntity', $result);
    }
}
