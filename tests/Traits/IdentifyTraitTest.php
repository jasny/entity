<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\AbstractIdentifiableEntity;
use Jasny\Entity\Tests\_Support\CreateEntityTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Traits\IdentifyTrait
 */
class IdentifyTraitTest extends TestCase
{
    use CreateEntityTrait;

    public function testGetId()
    {
        $entity = $this->createIdentifiableEntity('foo');

        $this->assertSame('foo', $entity->getId());
    }

    public function testGetIdException()
    {
        $entity = new class() extends AbstractIdentifiableEntity {
        };

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("Unknown id property");

        $entity->getId();
    }
}
