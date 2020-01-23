<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\Tests\_Support\CreateEntityTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Traits\SetTrait
 */
class SetTraitTest extends TestCase
{
    use CreateEntityTrait;

    /**
     * Test 'set' method for single value
     */
    public function testSetValue()
    {
        $object = $this->createBasicEntity();
        $object->set('foo', 'bar');

        $this->assertEquals('bar', $object->foo);
        $this->assertEquals(0, $object->bar);
    }

    /**
     * Test 'set' method with null value
     */
    public function testSetValueToNull()
    {
        $object = $this->createBasicEntity();
        $object->set('bar', null);

        $this->assertEquals(null, $object->foo);
        $this->assertEquals(null, $object->bar);
    }

    /**
     * Test 'set' method for multiple values
     */
    public function testSetValues()
    {
        $object = $this->createBasicEntity();
        $object->set(['foo' => 'bar', 'bar' => 100, 'dyn' => 'woof']);

        $this->assertEquals('bar', $object->foo);
        $this->assertEquals(100, $object->bar);
        $this->assertObjectNotHasAttribute('dyn', $object);
    }

    /**
     * Test 'set' method for multiple values and dynamic entity
     */
    public function testSetValuesDynamic()
    {
        $object = $this->createDynamicEntity();
        $object->set(['foo' => 'bar', 'bar' => 100, 'dyn' => 'woof']);

        $this->assertEquals('bar', $object->foo);
        $this->assertEquals(100, $object->bar);
        $this->assertEquals('woof', $object->dyn);
    }

    public function testSetValueWithOnlyKey()
    {
        $object = $this->createDynamicEntity();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage("If first argument is a string, a second argument is required");

        $object->set('foo');
    }

    public function testSetValueWithKeyAndArray()
    {
        $object = $this->createDynamicEntity();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage("If first argument is an array, the second argument must be omitted");

        $object->set(['foo' => 'bar'], 42);
    }
}
