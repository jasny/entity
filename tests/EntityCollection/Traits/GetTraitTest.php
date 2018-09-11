<?php

namespace Jasny\Tests\EntityCollection\Traits;

use Jasny\EntityCollection\Traits\FindEntityTrait;
use Jasny\TestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Jasny\Entity\EntityInterface;
use Jasny\EntityCollection\Traits\GetTrait;

/**
 * @covers \Jasny\EntityCollection\Traits\GetTrait
 * @covers \Jasny\EntityCollection\Traits\FindEntityTrait
 */
class GetTraitTest extends TestCase
{
    use TestHelper;

    /**
     * @var GetTrait
     */
    protected $collection;

    /**
     * @var EntityInterface[]|MockObject[]
     */
    protected $entities;

    /**
     * Set up dependencies before each test
     */
    public function setUp()
    {
        $this->collection = $this->getMockForTrait(GetTrait::class);

        $this->entities = [
            $this->createMock(EntityInterface::class),
            $this->createMock(EntityInterface::class),
            $this->createMock(EntityInterface::class)
        ];
    }

    public function testContainsByRef()
    {
        $this->entities[0]->expects($this->any())->method('is')->with($this->entities[1])->willReturn(false);
        $this->entities[1]->expects($this->any())->method('is')->with($this->entities[1])->willReturn(true);
        $this->entities[2]->expects($this->any())->method('is')->with($this->entities[1])->willReturn(false);

        $this->setPrivateProperty($this->collection, 'entities', $this->entities);

        $this->assertTrue($this->collection->contains($this->entities[1]));
    }

    public function testContainsByRefNotFound()
    {
        $otherEntity = $this->createMock(EntityInterface::class);

        $this->entities[0]->expects($this->any())->method('is')->with($otherEntity)->willReturn(false);
        $this->entities[1]->expects($this->any())->method('is')->with($otherEntity)->willReturn(false);
        $this->entities[2]->expects($this->any())->method('is')->with($otherEntity)->willReturn(false);

        $this->setPrivateProperty($this->collection, 'entities', $this->entities);

        $this->assertFalse($this->collection->contains($otherEntity));
    }

    public function testContainsById()
    {
        $this->entities[0]->expects($this->any())->method('is')->with('two')->willReturn(false);
        $this->entities[1]->expects($this->any())->method('is')->with('two')->willReturn(true);
        $this->entities[2]->expects($this->any())->method('is')->with('two')->willReturn(false);

        $this->setPrivateProperty($this->collection, 'entities', array_values($this->entities));

        $this->assertTrue($this->collection->contains('two'));
    }

    public function testContainsByIdNotFound()
    {
        $this->entities[0]->expects($this->any())->method('is')->with('not')->willReturn(false);
        $this->entities[1]->expects($this->any())->method('is')->with('not')->willReturn(false);
        $this->entities[2]->expects($this->any())->method('is')->with('not')->willReturn(false);

        $this->setPrivateProperty($this->collection, 'entities', array_values($this->entities));

        $this->assertFalse($this->collection->contains('not'));
    }


    public function testGetByRef()
    {
        $this->entities[0]->expects($this->any())->method('is')->with($this->entities[1])->willReturn(false);
        $this->entities[1]->expects($this->any())->method('is')->with($this->entities[1])->willReturn(true);
        $this->entities[2]->expects($this->any())->method('is')->with($this->entities[1])->willReturn(false);

        $this->setPrivateProperty($this->collection, 'entities', $this->entities);

        $this->assertSame($this->entities[1], $this->collection->get($this->entities[1]));
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetByRefNotFound()
    {
        $otherEntity = $this->createMock(EntityInterface::class);

        $this->entities[0]->expects($this->any())->method('is')->with($otherEntity)->willReturn(false);
        $this->entities[1]->expects($this->any())->method('is')->with($otherEntity)->willReturn(false);
        $this->entities[2]->expects($this->any())->method('is')->with($otherEntity)->willReturn(false);

        $this->setPrivateProperty($this->collection, 'entities', $this->entities);

        $this->collection->get($otherEntity);
    }
}
