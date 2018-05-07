<?php

namespace Jasny\Entity;

use Jasny\Support\TestEntity;
use Jasny\Support\DynamicTestEntity;
use PHPUnit\Framework\TestCase;

/**
 * @covers Jasny\Entity\SetStateTrait
 */
class SetStateTraitTest extends TestCase
{
    public function testSetState()
    {
        $entity = TestEntity::__set_state(['foo' => 'bar', 'num' => 22, 'dyn' => 'woof']);
        
        $this->assertInstanceOf(TestEntity::class, $entity);
        
        $this->assertAttributeSame('bar', 'foo', $entity);
        $this->assertAttributeSame(22, 'num', $entity);
        
        $this->assertObjectNotHasAttribute('dyn', $entity);
    }
    
    public function testSetStateWithDynamicEntity()
    {
        $entity = DynamicTestEntity::__set_state(['foo' => 'bar', 'num' => 22, 'dyn' => 'woof']);
        
        $this->assertInstanceOf(DynamicTestEntity::class, $entity);
        
        $this->assertAttributeSame('bar', 'foo', $entity);
        $this->assertAttributeSame(22, 'num', $entity);
        $this->assertAttributeSame('woof', 'dyn', $entity);
    }
    
    public function testSetStateConstruct()
    {
        $entity = TestEntity::__set_state([]);
        
        $this->assertInstanceOf(TestEntity::class, $entity);
        
        $this->assertAttributeSame(0, 'num', $entity);
    }
}
