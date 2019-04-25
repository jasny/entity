<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\AbstractBasicEntity;
use Jasny\Entity\DynamicEntity;
use Jasny\Entity\Entity;
use Jasny\Entity\EntityTraits;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Traits\ToJsonTrait
 */
class JsonSerializeTraitTest extends TestCase
{
    /**
     * @var AbstractBasicEntity
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = new class() implements Entity {
            use EntityTraits;

            public $foo;
            public $color;
        };
    }

    /**
     * Test 'jsonSerialize' method
     */
    public function testJsonSerialize()
    {
        $this->entity->foo = 'bar';
        $this->entity->color = 'blue';
        $this->entity->non_exist = 'zoo';
        $expected = (object)['foo' => 'bar', 'color' => 'blue'];

        $result = $this->entity->jsonSerialize();

        $this->assertEquals($expected, $result);
    }

    /**
     * Test 'jsonSerialize' method for dynamic entity
     */
    public function testJsonSerializeDynamic()
    {
        $this->entity = new class() implements DynamicEntity {
            use EntityTraits;
        };

        $this->entity->foo = 'bar';
        $this->entity->color = 'blue';
        $expected = (object)['foo' => 'bar', 'color' => 'blue'];

        $result = $this->entity->jsonSerialize();

        $this->assertEquals($expected, $result);
    }
}
