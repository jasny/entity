<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\BasicEntityTraits;
use Jasny\Entity\IdentifiableEntityTraits;
use Jasny\Entity\DynamicEntity;
use Jasny\Entity\Entity;
use Jasny\Entity\IdentifiableEntity;
use Jasny\Entity\Tests\CreateEntityTrait;
use Jasny\Entity\Traits\FromDataTrait;
use Jasny\TestHelper;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Traits\FromDataTrait
 */
class FromDataTraitTest extends TestCase
{
    use TestHelper;
    use CreateEntityTrait;

    protected function createIdentifiableEntity(): IdentifiableEntity
    {
        return new class() implements IdentifiableEntity {
            use IdentifiableEntityTraits;

            public $id;
            public $foo;
            public $num;

            public function __construct()
            {
                $this->num = 0;
            }
        };
    }


    /**
     * Test 'fromData' method for non-dynamic entity
     */
    public function testFromData()
    {
        $source = $this->createBasicEntity();
        $class = get_class($source);

        $entity = $class::fromData(['foo' => 'loo', 'bar' => 22, 'dyn' => 'woof']);

        $this->assertInstanceOf($class, $entity);

        $this->assertEquals('loo', $entity->foo);
        $this->assertEquals(22, $entity->bar);

        $this->assertObjectNotHasAttribute('dyn', $entity);
    }

    /**
     * Test 'fromData' method for dynamic entity
     */
    public function testSetStateWithDynamicEntity()
    {
        $source = $this->createDynamicEntity();
        $class = get_class($source);

        $entity = $class::fromData(['foo' => 'loo', 'bar' => 22, 'dyn' => 'woof']);

        $this->assertInstanceOf($class, $entity);
        $this->assertEquals('loo', $entity->foo);
        $this->assertEquals(22, $entity->bar);

        $this->assertObjectHasAttribute('dyn', $entity);
        $this->assertEquals('woof', $entity->dyn);
    }

    /**
     * Test 'fromData' method when constructor should be called
     */
    public function testSetStateConstruct()
    {
        $source = $this->createEntityWithConstructor();
        $class = get_class($source);

        $entity = $class::fromData([]);

        $this->assertInstanceOf($class, $entity);
        $this->assertEquals(0, $entity->bar);
    }

    /**
     * Test 'markAsPersisted' method
     */
    public function testMarkAsPersisted()
    {
        $entity = $this->createBasicEntity();
        $this->assertTrue($entity->isNew());

        $entity->markAsPersisted();
        $this->assertFalse($entity->isNew());
    }
}
