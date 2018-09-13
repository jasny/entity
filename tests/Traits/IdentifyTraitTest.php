<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\AbstractIdentifiableEntity;
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
        $entity = new class() extends AbstractIdentifiableEntity {
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
        $entity = new class() extends AbstractIdentifiableEntity {
        };

        $entity->getId();
    }
}
