<?php

namespace Jasny\Entity;

use JsonSerializable;
use Jasny\EntityInterface;
use Jasny\Entity;
use Jasny\Entity\LazyLoadingInterface;
use Jasny\Entity\Traits\LazyLoadingTrait;
use Jasny\Entity\Traits\JsonSerializeTrait;
use Jasny\Entity\Traits\GetSetTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers Jasny\Entity\Traits\JsonSerializeTrait
 * @group entity
 */
class JsonSerializeTraitTest extends TestCase
{
    /**
     * @var EntityInterface|JsonSerializable
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = $this->getMockForTrait(JsonSerializeTrait::class);
    }

    public function testJsonSerialize()
    {
        $this->entity->foo = 'bar';
        $this->entity->color = 'blue';

        $expected = (object)['foo' => 'bar', 'color' => 'blue'];
        $this->entity->expects($this->once())->method('trigger')->with('jsonSerialize', $expected)->willReturn($expected);

        $result = $this->entity->jsonSerialize();

        $this->assertEquals($expected, $result);
    }

    public function testJsonSerializeCastDateTime()
    {
        $data = (object)['date' => new \DateTime('2013-03-01 16:04:00 +01:00'), 'color' => 'pink'];
        $expected = (object)['date' => '2013-03-01T16:04:00+0100', 'color' => 'pink'];

        $this->entity->date = $data->date;
        $this->entity->color = $data->color;
        $this->entity->expects($this->once())->method('trigger')->with('jsonSerialize', $data)->willReturn($expected);

        $result = $this->entity->jsonSerialize();

        $this->assertEquals($expected, $result);
    }

    public function testJsonSerializeCastJsonSerializable()
    {
        $entity = $this->entity;
        $entity->foo = $this->getMockForAbstractClass(\JsonSerializable::class);
        $entity->foo->expects($this->once())->method('jsonSerialize')->willReturn('bar');

        $expected = (object)['foo' => 'bar'];
        $entity->method('trigger')->with('jsonSerialize', (object)['foo' => $entity->foo])->willReturn($expected);

        $result = $entity->jsonSerialize();

        $this->assertEquals($expected, $result);
    }
}
