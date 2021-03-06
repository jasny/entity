<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\AbstractIdentifiableEntity;
use Jasny\Entity\Tests\_Support\CreateEntityTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Traits\CompareTrait
 */
class CompareTraitTest extends TestCase
{
    use CreateEntityTrait;

    public function testIsSameObject()
    {
        $entity = $this->createBasicEntity();
        $same = $entity;

        $this->assertTrue($entity->is($same));
    }

    public function testIsNotSameObject()
    {
        $entity = $this->createBasicEntity();
        $other = $this->createBasicEntity();

        $this->assertFalse($entity->is($other));
    }


    public function testIsWithId()
    {
        $entity = $this->createIdentifiableEntity(42);

        $this->assertTrue($entity->is(42));
    }

    public function testIsWithIdNotStrict()
    {
        $entity = $this->createIdentifiableEntity(42);

        $this->assertTrue($entity->is("42"));
    }

    public function testIsNotWithId()
    {
        $entity = $this->createIdentifiableEntity(42);

        $this->assertFalse($entity->is(21));
    }

    public function testIsNotWithIdNotIdentifiable()
    {
        $entity = $this->createBasicEntity();

        $this->assertFalse($entity->is(21));
    }

    public function testIsWithIdentifiable()
    {
        $entity = $this->createIdentifiableEntity(42);
        $entity->foo = 10;

        $same = clone $entity;
        $same->foo = 12;

        $this->assertTrue($entity->is($same));
    }

    public function testIsNotWithIdentifiable()
    {
        $entity = $this->createIdentifiableEntity(42);
        $entity->foo = 10;

        $same = clone $entity;
        $same->id = 12;
        $same->foo = 12;

        $this->assertFalse($entity->is($same));
    }

    public function testIsNotWithOtherClass()
    {
        $entity = $this->createIdentifiableEntity(42);
        $other = new class() extends AbstractIdentifiableEntity {
            public $id = 42;
        };

        $this->assertFalse($entity->is($other));
    }


    public function testIsWithArrayId()
    {
        $entity = $this->createIdentifiableEntity(['foo' => 10, 'bar' => 99]);

        $this->assertTrue($entity->is(['foo' => 10, 'bar' => 99]));
        $this->assertFalse($entity->is(['foo' => 21, 'bar' => 99]));
    }

    public function testIsWithArrayIdNoStrict()
    {
        $entity = $this->createIdentifiableEntity(['foo' => 10, 'bar' => 99]);

        $this->assertTrue($entity->is(['foo' => 10, 'bar' => '99']));
        $this->assertFalse($entity->is(['foo' => 21, 'bar' => '99']));
    }
}
