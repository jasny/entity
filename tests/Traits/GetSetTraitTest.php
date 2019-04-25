<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\AbstractBasicEntity;
use Jasny\Entity\DynamicEntity;
use Jasny\Entity\Entity;
use Jasny\Entity\EntityTraits;
use Jasny\TestHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Traits\SetTrait
 */
class GetSetTraitTest extends TestCase
{
    use TestHelper;

    protected function createObject()
    {
        return new class() implements Entity {
            use EntityTraits;
            public $foo;
            public $num = 0;
        };
    }

    protected function createDynamicObject()
    {
        return new class() implements DynamicEntity {
            use EntityTraits;
            public $foo;
            public $num = 0;
        };
    }


    /**
     * Test 'set' method for single value
     */
    public function testSetValue()
    {
        $object = $this->createObject();
        $object->set('foo', 'bar');

        $this->assertEquals('bar', $object->foo);
        $this->assertEquals(0, $object->num);
    }

    /**
     * Test 'set' method with null value
     */
    public function testSetValueToNull()
    {
        $object = $this->createObject();
        $object->set('num', null);

        $this->assertEquals(null, $object->foo);
        $this->assertEquals(null, $object->num);
    }

    /**
     * Test 'set' method for multiple values
     */
    public function testSetValues()
    {
        $object = $this->createObject();
        $object->set(['foo' => 'bar', 'num' => 100, 'dyn' => 'woof']);

        $this->assertEquals('bar', $object->foo);
        $this->assertEquals(100, $object->num);
        $this->assertObjectNotHasAttribute('dyn', $object);
    }

    /**
     * Test 'set' method for multiple values and dynamic entity
     */
    public function testSetValuesDynamic()
    {
        $object = $this->createDynamicObject();
        $object->set(['foo' => 'bar', 'num' => 100, 'dyn' => 'woof']);

        $this->assertEquals('bar', $object->foo);
        $this->assertEquals(100, $object->num);
        $this->assertEquals('woof', $object->dyn);
    }

    /**
     * Test 'set' method with invalid argument
     *
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage If first argument is a string, a second argument is required
     */
    public function testSetValueInvalidArgument()
    {
        $object = $this->createDynamicObject();
        $object->set('foo');
    }

    /**
     * Test 'toAssoc' method
     */
    public function testToAssoc()
    {
        $expected = ['foo' => 'bar', 'num' => 23];

        $object = $this->createDynamicObject();

        $object->foo = 'bar';
        $object->num = 23;

        $result = $object->toAssoc();

        $this->assertSame($expected, $result);
    }
}
