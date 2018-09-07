<?php

namespace Jasny\Tests\Entity\Traits;

use Jasny\Tests\Support\TestEntity;
use Jasny\Tests\Support\DynamicTestEntity;
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
        $entity = TestEntity::__set_state(['foo' => 'bar', 'num' => 22, 'dyn' => 'woof']);

        $this->assertInstanceOf(TestEntity::class, $entity);

        $this->assertAttributeSame('bar', 'foo', $entity);
        $this->assertAttributeSame(22, 'num', $entity);

        $this->assertObjectNotHasAttribute('dyn', $entity);
    }

    /**
     * Test '__set_state' method for dynamic entity
     */
    public function testSetStateWithDynamicEntity()
    {
        $entity = DynamicTestEntity::__set_state(['foo' => 'bar', 'num' => 22, 'dyn' => 'woof']);

        $this->assertInstanceOf(DynamicTestEntity::class, $entity);

        $this->assertAttributeSame('bar', 'foo', $entity);
        $this->assertAttributeSame(22, 'num', $entity);
        $this->assertAttributeSame('woof', 'dyn', $entity);
    }

    /**
     * Test '__set_state' method when constructor should be called
     */
    public function testSetStateConstruct()
    {
        $entity = TestEntity::__set_state([]);

        $this->assertInstanceOf(TestEntity::class, $entity);

        $this->assertAttributeSame(0, 'num', $entity);
    }

    /**
     * Test 'markAsPersisted' method
     */
    public function testMarkAsPersisted()
    {
        $entity = $this->createPartialMock(TestEntity::class, ['trigger']);
        $entity->expects($this->once())->method('trigger')->with('persisted');

        $result = $entity->markAsPersisted();

        $this->assertSame($entity, $result);
        $this->assertFalse($entity->isNew());
    }
}
