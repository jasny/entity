<?php

namespace Jasny\Tests\Entity\Traits;

use Jasny\Tests\Support\TestEntity;
use Jasny\Tests\Support\IdentifyTestEntity;
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
        return [
            [IdentifyTestEntity::class, true],
            [TestEntity::class, false]
        ];
    }

    /**
     * Test 'hasIdProperty' method
     *
     * @dataProvider hasIdPropertyProvider
     */
    public function testHasIdProperty($class, $expected)
    {
        $entity = $this->createPartialMock($class, []);
        $result = $entity->hasIdProperty();

        $this->assertSame($expected, $result);
    }

    /**
     * Test 'getId' method
     */
    public function testGetId()
    {
        $entity = $this->createPartialMock(IdentifyTestEntity::class, []);
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
        $entity = $this->createPartialMock(TestEntity::class, []);
        $result = $entity->getId();
    }
}
