<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\IdentifiableEntity;
use Jasny\Entity\IdentifiableEntityTraits;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Traits\IdentifyTrait
 */
class IdentifyTraitTest extends TestCase
{
    /**
     * Test 'getId' method
     */
    public function testGetId()
    {
        $entity = new class() implements IdentifiableEntity {
            use IdentifiableEntityTraits;
            public $id = 'foo';
        };

        $this->assertSame('foo', $entity->getId());
    }

    /**
     * Test 'getId' method, in case when id is not set
     *
     * @expectedException \LogicException
     * @expectedExceptionMessag Unknown id property
     */
    public function testGetIdException()
    {
        $entity = new class() implements IdentifiableEntity {
            use IdentifiableEntityTraits;
        };

        $entity->getId();
    }
}
