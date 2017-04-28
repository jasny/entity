<?php

namespace Jasny\Entity;

use Jasny\EntityInterface;
use Jasny\Entity\SetterTrait;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Jasny\Entity\JsonSerializeTrait
 */
class SetterTraitTest extends TestCase
{
    /**
     * @var EntityInterface
     */
    protected $entity;
    
    public function setUp()
    {
        $this->entity = $this->getMockForTrait(SetterTrait::class);
    }
    
    public function testSetValue()
    {
        $this->entity->set('foo', 'bar');
        
        $this->assertAttributeEquals('bar', 'foo', $this->entity);
    }
    
    public function testSetValueToNull()
    {
        $this->entity->set('foo', null);
        
        $this->assertAttributeEquals(null, 'foo', $this->entity);
    }
    
    public function testSetValues()
    {
        $this->entity->set(['foo' => 'bar', 'color' => 'blue']);
        
        $this->assertAttributeEquals('bar', 'foo', $this->entity);
        $this->assertAttributeEquals('blue', 'color', $this->entity);
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Expected an array or stdClass object, but got a string
     */
    public function testSetValueInvalidArgument()
    {
        $this->entity->set('foo');
    }
}
