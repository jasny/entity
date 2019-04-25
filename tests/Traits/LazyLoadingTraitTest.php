<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\AbstractBasicEntity;
use Jasny\Entity\IdentifiableEntity;
use Jasny\Entity\IdentifiableEntityTraits;
use Jasny\Entity\DynamicEntity;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Traits\LazyLoadingTrait
 */
class LazyLoadingTraitTest extends TestCase
{
    /**
     * @var IdentifiableEntityTraits
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = new class() implements IdentifiableEntity {
            use IdentifiableEntityTraits;

            public $id;
            public $bar = 10;
        };
    }

    /**
     * Test 'isGhost' method
     */
    public function testIsGhost()
    {
        $this->assertFalse($this->entity->isGhost());
    }

    /**
     * Test 'fromId' method
     */
    public function testFromId()
    {
        $class = get_class($this->entity);
        $entity = $class::fromId('foo');

        $this->assertInstanceOf($class, $entity);
        $this->assertTrue($entity->isGhost());

        $this->assertSame('foo', $entity->getId());
        $this->assertArrayNotHasKey('bar', (array)$entity);
    }
}
