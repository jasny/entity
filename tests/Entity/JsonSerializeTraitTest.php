<?php

namespace Jasny\Entity;

use JsonSerializable;
use Jasny\EntityInterface;
use Jasny\Entity;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Jasny\Entity\JsonSerializeTrait
 */
class JsonSerializeTraitTest extends TestCase
{
    /**
     * @var EntityInterface|JsonSerializable
     */
    protected $entity;
    
    public function setUp()
    {
        $this->entity = $this->getMockForTrait(Entity\JsonSerializeTrait::class);
    }
    
    public function testJsonSerialize()
    {
        $this->entity->foo = 'bar';
        $this->entity->color = 'blue';
        
        $this->assertEquals((object)['foo' => 'bar', 'color' => 'blue'], $this->entity->jsonSerialize());
    }
    
    public function testJsonSerializeCastDateTime()
    {
        $this->entity->date = new \DateTime('2013-03-01 16:04:00 +01:00');
        $this->entity->color = 'pink';
        
        $this->assertEquals(
            (object)['date' => '2013-03-01T16:04:00+0100', 'color' => 'pink'],
            $this->entity->jsonSerialize()
        );
    }
    
    public function testJsonSerializeCastJsonSerializable()
    {
        $this->entity->foo = $this->getMockForAbstractClass(\JsonSerializable::class);
        $this->entity->foo->expects($this->once())->method('jsonSerialize')->willReturn('bar');
        
        $this->assertEquals((object)['foo' => 'bar'], $this->entity->jsonSerialize());
    }
    
    
    public function testJsonSerializeFilter()
    {
        $this->entity = $this->getMockForTrait(JsonSerializeTrait::class, [], '', true, true, true, [
            'jsonSerializeFilterFoo',
            'jsonSerializeFilterBar'
        ]);
        $this->entity->expects($this->once())->method('jsonSerializeFilterFoo')->with((object)['foo' => 'bar']);
        $this->entity->expects($this->once())->method('jsonSerializeFilterBar')->with((object)['foo' => 'bar']);
        
        $this->entity->foo = 'bar';
        
        $this->entity->jsonSerialize();
    }
    
    
    protected function createLazyLoadingEntity(): JsonSerializable
    {
        return new class implements JsonSerializable, Entity\WithLazyLoading {
            use Entity\JsonSerializeTrait,
                Entity\ToAssocTrait;
            
            public function isGhost()
            {
                return !isset($this->foo);
            }
            
            public function expand()
            {
                $this->foo = 'bar';
            }
        };
    }
    
    public function testJsonSerializeExpand()
    {
        $entity = $this->createLazyLoadingEntity();
        
        $this->assertEquals(
            (object)['foo' => 'bar'],
            $entity->jsonSerialize()
        );
    }
    
    
    /**
     * @ignore
     * @internal IDE is acting up because of anonymous class with use statement
     */
    public function toArray()
    {
    }
}
