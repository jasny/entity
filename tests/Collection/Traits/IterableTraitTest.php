<?php

namespace Jasny\EntityCollection\Tests\Traits;

use PHPUnit\Framework\TestCase;
use Jasny\Entity\Entity;
use Jasny\EntityCollection\Traits\TraversableTrait;

/**
 * @covers \Jasny\EntityCollection\Traits\TraversableTrait
 */
class IterableTraitTest extends TestCase
{
    /**
     * TraversableTrait|\IteratorAggregate
     */
    public $collection;

    /**
     * @var Entity[]|MockObject[]
     */
    protected $entities;

    /**
     * Set up dependencies before each test
     */
    public function setUp()
    {
        $this->entities = [
            $this->createMock(Entity::class),
            $this->createMock(Entity::class),
            $this->createMock(Entity::class)
        ];

        $this->collection = new class($this->entities) implements \IteratorAggregate {
            use TraversableTrait;

            public function __construct(array $entities)
            {
                $this->entities = $entities;
            }
        };
    }

    /**
     * Test 'getIterator' method
     */
    public function testIterate()
    {
        $result = [];

        foreach ($this->collection as $entity) {
            $result[] = $entity;
        }

        $this->assertSame($this->entities, $result);
    }

    /**
     * Test 'toArray' method
     */
    public function testToArray()
    {
        $result = $this->collection->toArray();

        $this->assertSame($this->entities, $result);
    }
}
