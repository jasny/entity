<?php

namespace Jasny\Entity;

use Jasny\Entity;
use PHPUnit_Framework_TestCase as TestCase;
use Jasny\Meta\Introspection;

/**
 * @covers Jasny\Entity\ChangeAwarenessTrait
 */
class ChangeAwarenessTraitTest extends TestCase
{
    /**
     * @var Entity\ChangeAwarenessTrait
     */
    protected $entity;
    
    public function setUp()
    {
        $this->entity = $this->getMockForTrait(Entity\ChangeAwarenessTrait::class);
        
        $this->entity->foo = 'bar';
        $this->entity->amount = 10;
    }
    
    
    public function testIsNew()
    {
        $this->assertTrue($this->entity->isNew());
        
        $this->entity->markAsPersisted();
        $this->assertFalse($this->entity->isNew());
        
        $this->entity->foo = 'baz';
        $this->assertFalse($this->entity->isNew());
    }
    
    public function testIsModified()
    {
        $this->assertTrue($this->entity->isModified());
        
        $this->entity->markAsPersisted();
        $this->assertFalse($this->entity->isModified()); // Fails because we need to create an EntityComparator that ignores protected properties
        
        $this->entity->foo = 'baz';
        $this->assertTrue($this->entity->isModified());
    }
    
    public function testHasModified()
    {
        $this->assertTrue($this->entity->hasModified('foo'));
        
        $this->entity->markAsPersisted();
        $this->assertFalse($this->entity->hasModified('foo'));
        $this->assertFalse($this->entity->hasModified('amount'));
        
        $this->entity->foo = 'baz';
        $this->assertTrue($this->entity->hasModified('foo'));
        $this->assertFalse($this->entity->hasModified('amount'));
        
        $this->entity->amount = '10';
        $this->assertFalse($this->entity->hasModified('amount'));
    }
    
    public function testGetChanges()
    {
        $this->assertEquals(['foo' => 'bar', 'amount' => 10], $this->entity->getChanges());
        
        $this->entity->markAsPersisted();
        $this->assertEquals([], $this->entity->getChanges());
        
        $this->entity->foo = 'baz';
        $this->assertEquals(['foo' => 'baz'], $this->entity->getChanges());
        
        $this->entity->amount = '10';
        $this->assertEquals(['foo' => 'baz'], $this->entity->getChanges());
    }
    
    public function testGetUnmodifiedCopy()
    {
        $this->assertNull($this->entity->getUnmodifiedCopy());
        
        $this->entity->markAsPersisted();
        
        $copy1 = $this->entity->getUnmodifiedCopy();
        $this->assertNotSame($this->entity, $copy1);
        $this->assertEquals(get_object_vars($this->entity), get_object_vars($copy1));
        
        $copy1->foo = 'wiz';
        $this->assertNotSame($copy1, $this->entity->getUnmodifiedCopy());
        
        $copy2 = $this->entity->getUnmodifiedCopy();
        $this->assertAttributeEquals('bar', 'foo', $copy2);
        
        $this->entity->foo = 'baz';
        $copy3 = $this->entity->getUnmodifiedCopy();
        $this->assertNotSame($copy2, $copy3);
        $this->assertEquals(get_object_vars($copy2), get_object_vars($copy3));
    }
    
    
    protected function createIntrospectionEntity(): Entity\WithChangeAwareness
    {
        return new class implements Entity\WithChangeAwareness, Introspection {
            use Entity\ChangeAwarenessTrait,
                Entity\IntrospectionTrait;
            
            public $foo;
            
            /** @ignore */
            public $wot;
            
            protected $wat;
            
            public function setWat($wat) {
                $this->wat = $wat;
            }
        };
    }
    
    public function testIsModifiedWithIgnoredProperty()
    {
        $entity = $this->createIntrospectionEntity();
        $entity->markAsPersisted();
        
        $entity->wot = 10;
        $this->assertFalse($entity->isModified()); // Fails because we need to create an EntityComparator that ignores ignored properties
    }
    
    public function testIsModifiedWithProtectedProperty()
    {
        $entity = $this->createIntrospectionEntity();
        $entity->markAsPersisted();
        
        $entity->setWat(22);
        $this->assertFalse($entity->isModified()); // Fails because we need to create an EntityComparator that ignores privated properties
    }
    
    public function testGetChangesWithIgnoredProperty()
    {
        $entity = $this->createIntrospectionEntity();
        $entity->markAsPersisted();

        $entity->foo = 'zoo';
        $entity->wot = 10;
        $entity->setWat(22);
        
        $this->assertEquals(['foo' => 'zoo'], $entity->getChanges());
    }
}
