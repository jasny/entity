<?php

namespace Jasny\Entity;

use Jasny\EntityInterface;
use Jasny\Support\TestEntity;
use Jasny\Support\DynamicTestEntity;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Jasny\Entity\SetterTrait
 */
class SetterTraitTest extends TestCase
{
    /**
     * @var EntityInterface
     */
    protected $entity;
    
    public function setUp()
    {
        $this->entity = new TestEntity();
    }
    
    public function testSetValue()
    {
        $this->entity->set('foo', 'bar');
        
        $this->assertAttributeSame('bar', 'foo', $this->entity);
        $this->assertAttributeSame(0, 'num', $this->entity);
    }
    
    public function testSetValueToNull()
    {
        $this->entity->set('num', null);
        
        $this->assertAttributeSame(null, 'foo', $this->entity);
        $this->assertAttributeSame(null, 'num', $this->entity);
    }
    
    public function testSetValues()
    {
        $this->entity->set(['foo' => 'bar', 'num' => 100, 'dyn' => 'woof']);
        
        $this->assertAttributeSame('bar', 'foo', $this->entity);
        $this->assertAttributeSame(100, 'num', $this->entity);
        $this->assertObjectNotHasAttribute('dyn', $this->entity);
    }
    
    public function testSetValuesDynamic()
    {
        $this->entity = new DynamicTestEntity();
        
        $this->entity->set(['foo' => 'bar', 'num' => 100, 'dyn' => 'woof']);
        
        $this->assertAttributeSame('bar', 'foo', $this->entity);
        $this->assertAttributeSame(100, 'num', $this->entity);
        $this->assertAttributeSame('woof', 'dyn', $this->entity);
    }
    
    /**
     * @expectedException TypeError
     * @expectedExceptionMessage Expected array or stdClass object, string given
     */
    public function testSetValueInvalidArgument()
    {
        $this->entity->set('foo');
    }
}
