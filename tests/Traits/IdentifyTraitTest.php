<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\AbstractIdentifiableEntity;
use Jasny\Entity\Tests\CreateEntityTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Traits\IdentifyTrait
 */
class IdentifyTraitTest extends TestCase
{
    use CreateEntityTrait;

    /**
     * Test 'getId' method
     */
    public function testGetId()
    {
        $entity = $this->createIdentifiableEntity('foo');

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
