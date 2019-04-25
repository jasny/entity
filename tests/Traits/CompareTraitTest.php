<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\AbstractBasicEntity;
use Jasny\Entity\Entity;
use Jasny\Entity\EntityTraits;
use Jasny\Entity\IdentifiableEntity;
use Jasny\Entity\IdentifiableEntityTraits;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Traits\CompareTrait
 */
class CompareTraitTest extends TestCase
{
    public function testIsSameObject()
    {
        $object = new class() implements Entity {
            use EntityTraits;
        };
        $same = $object;

        $this->assertTrue($object->is($same));
    }

    public function testIsNotSameObject()
    {
        $object = new class() implements Entity {
            use EntityTraits;
        };
        $other = new class() implements Entity {
            use EntityTraits;
        };

        $this->assertFalse($object->is($other));
    }


    public function testIsWithId()
    {
        $object = new class() implements IdentifiableEntity {
            use IdentifiableEntityTraits;
            public $id = 42;
        };

        $this->assertTrue($object->is(42));
    }

    public function testIsWithIdNotStrict()
    {
        $object = new class() implements IdentifiableEntity {
            use IdentifiableEntityTraits;
            public $id = 42;
        };

        $this->assertTrue($object->is("42"));
    }

    public function testIsNotWithId()
    {
        $object = new class() implements IdentifiableEntity {
            use IdentifiableEntityTraits;
            public $id = 42;
        };

        $this->assertFalse($object->is(21));
    }

    public function testIsNotWithIdNotIdentifiable()
    {
        $object = new class() implements Entity {
            use EntityTraits;
        };

        $this->assertFalse($object->is(21));
    }

    public function testIsWithIdentifiable()
    {
        $object = new class() implements IdentifiableEntity {
            use IdentifiableEntityTraits;
            public $id = 42;
            public $foo = 10;
        };

        $same = clone $object;
        $same->foo = 12;

        $this->assertTrue($object->is($same));
    }

    public function testIsNotWithIdentifiable()
    {
        $object = new class() implements IdentifiableEntity {
            use IdentifiableEntityTraits;
            public $id = 42;
            public $foo = 10;
        };

        $same = clone $object;
        $same->id = 12;
        $same->foo = 12;

        $this->assertFalse($object->is($same));
    }

    public function testIsNotWithOtherClass()
    {
        $object = new class() implements IdentifiableEntity {
            use IdentifiableEntityTraits;
            public $id = 42;
        };
        $other = new class() implements IdentifiableEntity {
            use IdentifiableEntityTraits;
            public $id = 42;
        };

        $this->assertFalse($object->is($other));
    }


    public function testIsWithFilter()
    {
        $object = new class() implements Entity {
            use EntityTraits;
            public $foo = 42;
            public $bar = 99;
        };

        $this->assertTrue($object->is(['foo' => 42]));
        $this->assertTrue($object->is(['foo' => 42, 'bar' => 99]));
    }

    public function testIsWithFilterNotStrict()
    {
        $object = new class() implements Entity {
            use EntityTraits;
            public $foo = 42;
        };

        $this->assertTrue($object->is(['foo' => "42"]));
    }

    public function testIsNotWithFilter()
    {
        $object = new class() implements Entity {
            use EntityTraits;
            public $foo = 42;
            public $bar = 99;
        };

        $this->assertFalse($object->is(['foo' => 21]));
        $this->assertFalse($object->is(['foo' => 21, 'bar' => 99]));
    }
}
