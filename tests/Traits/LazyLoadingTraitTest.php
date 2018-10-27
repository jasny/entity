<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\AbstractBasicEntity;
use Jasny\Entity\AbstractIdentifiableEntity;
use Jasny\Entity\DynamicEntity;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Traits\LazyLoadingTrait
 */
class LazyLoadingTraitTest extends TestCase
{
    /**
     * @var AbstractIdentifiableEntity
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = new class() extends AbstractIdentifiableEntity {
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
     * Test 'lazyload' method
     */
    public function testLazyload()
    {
        $class = get_class($this->entity);
        $entity = $class::lazyload('foo');

        $this->assertInstanceOf($class, $entity);
        $this->assertTrue($entity->isGhost());

        $this->assertSame('foo', $entity->getId());
    }
}
