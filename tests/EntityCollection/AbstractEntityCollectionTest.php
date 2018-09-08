<?php

namespace Jasny\Tests\EntityCollection;

use PHPUnit\Framework\TestCase;
use Jasny\Entity\EntityInterface;
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
        $collection = new class() extends AbstractEntityCollection {
            public $entityClass = EntityInterface::class;
        };

        $result = $collection->getEntityClass();

        $this->assertSame(EntityInterface::class, $result);
    }
}
