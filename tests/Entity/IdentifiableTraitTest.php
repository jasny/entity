<?php

namespace Jasny\Entity;

use Jasny\Support\TestEntity;
use Jasny\Support\IdentifiableTestEntity;
use PHPUnit\Framework\TestCase;

/**
 * @covers Jasny\Entity\Traits\IdentifiableTrait
 * @group entity
 */
class IdentifiableTraitTest extends TestCase
{
    /**
     * Provide data for testing 'isIdentifiable' method
     *
     * @return array
     */
    public function isIdentifiableProvider()
    {
        return [
            [IdentifiableTestEntity::class, true],
            [TestEntity::class, false]
        ];
    }

    /**
     * Test 'isIdentifiable' method
     *
     * @dataProvider isIdentifiableProvider
     */
    public function testIsIdentifiable($class, $expected)
    {
        $entity = $this->createPartialMock($class, []);
        $result = $entity->isIdentifiable();

        $this->assertSame($expected, $result);
    }

    /**
     * Test 'getId' method
     */
    public function testGetId()
    {
        $entity = $this->createPartialMock(IdentifiableTestEntity::class, []);
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
