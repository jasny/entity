<?php

namespace Jasny\Entity;

use Jasny\Entity;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Jasny\Entity\JsonSerializeTrait
 */
class SetStateTraitTest extends TestCase
{
    public function testSetState()
    {
        $class = $this->getMockClass(Entity::class);
        
        $entity = $class::__set_state(['foo' => 'bar', 'num' => 22]);
        
        $this->assertInstanceOf($class, $entity);
        $this->assertAttributeEquals('bar', 'foo', $entity);
        $this->assertAttributeEquals('num', 22, $entity);
    }
}
