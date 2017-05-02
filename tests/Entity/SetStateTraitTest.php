<?php

namespace Jasny\Entity;

use Jasny\SetStateTraitTestEntity;
use Jasny\SetStateTraitTestDynamicEntity;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Jasny\Entity\SetStateTrait
 */
class SetStateTraitTest extends TestCase
{
    public function testSetState()
    {
        $class = SetStateTraitTestEntity::class;
        $entity = $class::__set_state(['foo' => 'bar', 'num' => 22, 'dyn' => 'woof']);
        
        $this->assertInstanceOf($class, $entity);
        
        $this->assertAttributeEquals('bar', 'foo', $entity);
        $this->assertAttributeEquals(22, 'num', $entity);
        
        $this->assertObjectNotHasAttribute('dyn', $entity);
    }
    
    public function testSetStateWithDynamicEntity()
    {
        $class = SetStateTraitTestDynamicEntity::class;
        $entity = $class::__set_state(['foo' => 'bar', 'num' => 22, 'dyn' => 'woof']);
        
        $this->assertInstanceOf($class, $entity);
        
        $this->assertAttributeEquals('bar', 'foo', $entity);
        $this->assertAttributeEquals(22, 'num', $entity);
        $this->assertAttributeEquals('woof', 'dyn', $entity);
    }
}
