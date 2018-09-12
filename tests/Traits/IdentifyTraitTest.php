<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\Traits\IdentifyTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers Jasny\Entity\Traits\IdentifyTrait
 * @group entity
 */
class IdentifyTraitTest extends TestCase
{
    /**
     * Provide data for testing 'hasIdProperty' method
     *
     * @return array
     */
    public function hasIdPropertyProvider()
    {
        $entity1 = static::createIdentifiableObject();
        $entity2 = $this->getMockForTrait(IdentifyTrait::class);

        return [
            [$entity1, true],
            [$entity2, false]
        ];
    }

    /**
     * Test 'hasIdProperty' method
     *
     * @dataProvider hasIdPropertyProvider
     */
    public function testHasIdProperty($entity, $expected)
    {
        $result = $entity->hasIdProperty();

        $this->assertSame($expected, $result);
    }

    /**
     * Test 'getId' method
     */
    public function testGetId()
    {
        $entity = static::createIdentifiableObject();
        $entity->id = 'foo';

        $result = $entity->getId();

        $this->assertSame('foo', $result);
    }

    /**
     * Test 'getId' method, in case when id is not set
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessageRegExp /\w+ entity is not identifiable/
     */
    public function testGetIdException()
    {
        $entity = $this->getMockForTrait(IdentifyTrait::class);
        $result = $entity->getId();
    }

    /**
     * Get identifiable object
     *
     * @return object
     */
    protected static function createIdentifiableObject()
    {
        return new class() {
            use IdentifyTrait;

            public $id;
        };
    }
}
