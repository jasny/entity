<?php

namespace Jasny\Entity;

use Jasny\EntityInterface;
use Jasny\Support\TestEntity;
use Jasny\Support\DynamicTestEntity;
use PHPUnit\Framework\TestCase;

/**
 * @covers Jasny\Entity\Traits\GetSetTrait
 * @group entity
 */
class GetSetTraitTest extends TestCase
{
    /**
     * @var EntityInterface
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = new TestEntity();
    }

    /**
     * Test 'set' method for single value
     */
    public function testSetValue()
    {
        $this->entity->set('foo', 'bar');

        $this->assertAttributeSame('bar', 'foo', $this->entity);
        $this->assertAttributeSame(0, 'num', $this->entity);
    }

    /**
     * Test 'set' method with null value
     */
    public function testSetValueToNull()
    {
        $this->entity->set('num', null);

        $this->assertAttributeSame(null, 'foo', $this->entity);
        $this->assertAttributeSame(null, 'num', $this->entity);
    }

    /**
     * Test 'set' method for multiple values
     */
    public function testSetValues()
    {
        $this->entity->set(['foo' => 'bar', 'num' => 100, 'dyn' => 'woof']);

        $this->assertAttributeSame('bar', 'foo', $this->entity);
        $this->assertAttributeSame(100, 'num', $this->entity);
        $this->assertObjectNotHasAttribute('dyn', $this->entity);
    }

    /**
     * Test 'set' method for multiple values and dynamic entity
     */
    public function testSetValuesDynamic()
    {
        $this->entity = new DynamicTestEntity();

        $this->entity->set(['foo' => 'bar', 'num' => 100, 'dyn' => 'woof']);

        $this->assertAttributeSame('bar', 'foo', $this->entity);
        $this->assertAttributeSame(100, 'num', $this->entity);
        $this->assertAttributeSame('woof', 'dyn', $this->entity);
    }

    /**
     * Test 'set' method with invalid argument
     *
     * @expectedException TypeError
     * @expectedExceptionMessage Expected array, string given
     */
    public function testSetValueInvalidArgument()
    {
        $this->entity->set('foo');
    }

    /**
     * Test 'toAssoc' method
     */
    public function testToAssoc()
    {
        $expected = ['foo' => 'bar', 'num' => 23];

        $entity = $this->createPartialMock(TestEntity::class, ['trigger']);
        $entity->expects($this->once())->method('trigger')->with('toAssoc', $expected)->willReturn($expected);

        $entity->foo = $expected['foo'];
        $entity->num = $expected['num'];

        $result = $entity->toAssoc();

        $this->assertSame($expected, $result);
    }
}
