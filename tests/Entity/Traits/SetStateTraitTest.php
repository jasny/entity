<?php

namespace Jasny\Tests\Entity\Traits;

use Jasny\Entity\DynamicInterface;
use Jasny\Entity\Traits\SetStateTrait;
use Jasny\Entity\Traits\TriggerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers Jasny\Entity\Traits\SetStateTrait
 * @group entity
 */
class SetStateTraitTest extends TestCase
{
    /**
     * Test '__set_state' method for non-dynamic entity
     */
    public function testSetState()
    {
        $source = new class() {
            use SetStateTrait, TriggerTrait;

            public $foo;
            public $num = 0;
        };

        $class = get_class($source);

        $entity = $class::__set_state(['foo' => 'bar', 'num' => 22, 'dyn' => 'woof']);

        $this->assertInstanceOf($class, $entity);

        $this->assertAttributeSame('bar', 'foo', $entity);
        $this->assertAttributeSame(22, 'num', $entity);

        $this->assertObjectNotHasAttribute('dyn', $entity);
    }

    /**
     * Test '__set_state' method for dynamic entity
     */
    public function testSetStateWithDynamicEntity()
    {
        $source = new class() implements DynamicInterface {
            use SetStateTrait, TriggerTrait;

            public $foo;
            public $num = 0;
        };

        $class = get_class($source);

        $entity = $class::__set_state(['foo' => 'bar', 'num' => 22, 'dyn' => 'woof']);

        $this->assertInstanceOf($class, $entity);
        $this->assertAttributeSame('bar', 'foo', $entity);
        $this->assertAttributeSame(22, 'num', $entity);
        $this->assertAttributeSame('woof', 'dyn', $entity);
    }

    /**
     * Test '__set_state' method when constructor should be called
     */
    public function testSetStateConstruct()
    {
        $source = new class() implements DynamicInterface {
            use SetStateTrait, TriggerTrait;

            public $foo;
            public $num;

            public function __construct()
            {
                $this->num = 0;
            }
        };

        $class = get_class($source);

        $entity = $class::__set_state([]);

        $this->assertInstanceOf($class, $entity);
        $this->assertAttributeSame(0, 'num', $entity);
    }

    /**
     * Test 'markAsPersisted' method
     */
    public function testMarkAsPersisted()
    {
        $entity = $this->getMockForTrait(SetStateTrait::class);
        $entity->expects($this->once())->method('trigger')->with('persisted');

        $result = $entity->markAsPersisted();

        $this->assertSame($entity, $result);
        $this->assertFalse($entity->isNew());
    }
}
