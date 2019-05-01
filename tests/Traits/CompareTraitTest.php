<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\AbstractBasicEntity;
use Jasny\Entity\Entity;
use Jasny\Entity\AbstractBasicEntity;
use Jasny\Entity\IdentifiableEntity;
use Jasny\Entity\AbstractIdentifiableEntity;
use Jasny\Entity\Tests\CreateEntityTrait;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Traits\CompareTrait
 * @covers \Jasny\Entity\Traits\AssertGhostTrait
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
        $other = new class implements IdentifiableEntity {
            use AbstractIdentifiableEntity;
            public $id = 42;
        };

        $this->assertFalse($entity->is($other));
    }


    public function testIsWithFilter()
    {
        $entity = $this->createBasicEntity();
        $entity->foo = 10;
        $entity->bar = 99;

        $this->assertTrue($entity->is(['foo' => 10]));
        $this->assertTrue($entity->is(['foo' => 10, 'bar' => 99]));

        $this->assertFalse($entity->is(['foo' => 21]));
        $this->assertFalse($entity->is(['foo' => 21, 'bar' => 99]));
    }

    public function testIsWithFilterNotStrict()
    {
        $entity = $this->createBasicEntity();
        $entity->foo = 10;

        $this->assertTrue($entity->is(['foo' => "10"]));
    }


    public function testCompareAsGhost()
    {
        $fullEntity = $this->createIdentifiableEntity(12);
        $class = get_class($fullEntity);
        /** @var Entity $entity */
        $entity = $class::fromId(12);

        $this->assertTrue($entity->is($fullEntity));
    }

    public function testCompareAsGhostWithId()
    {
        $class = get_class($this->createIdentifiableEntity(''));
        /** @var Entity $entity */
        $entity = $class::fromId(12);

        $this->assertTrue($entity->is(12));
        $this->assertFalse($entity->is(10));
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage Trying to use ghost object
     */
    public function testCompareAsGhostWithFilter()
    {
        $class = get_class($this->createIdentifiableEntity(''));
        /** @var Entity $entity */
        $entity = $class::fromId(12);

        $entity->is(['foo' => 10]);
    }
}
