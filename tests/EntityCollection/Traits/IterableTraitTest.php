<?php

namespace Jasny\Tests\EntityCollection\Traits;

use PHPUnit\Framework\TestCase;
use Jasny\Entity\EntityInterface;
use Jasny\EntityCollection\Traits\IterableTrait;

/**
 * @covers \Jasny\EntityCollection\Traits\IterableTrait
 */
class IterableTraitTest extends TestCase
{
    /**
     * IterableTrait|\IteratorAggregate
     */
    public $collection;

    /**
     * @var EntityInterface[]|MockObject[]
     */
    protected $entities;

    /**
     * Set up dependencies before each test
     */
    public function setUp()
    {
        $this->entities = [
            $this->createMock(EntityInterface::class),
            $this->createMock(EntityInterface::class),
            $this->createMock(EntityInterface::class)
        ];

        $this->collection = new class($this->entities) implements \IteratorAggregate {
            use IterableTrait;

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
