<?php

namespace Jasny\Tests\EntityCollection;

use Jasny\Entity\EntityInterface;
use PHPUnit\Framework\TestCase;
use Jasny\Entity\Entity;
use Jasny\EntityCollection\EntitySet;

/**
 * @covers Jasny\EntityCollection\EntitySet
 */
class EntitySetTest extends TestCase
{
    use \Jasny\TestHelper;

    /**
     * @var EntityMap
     */
    protected $collection;

    /**
     * @var EntityInterface[]|MockObject[]
     */
    protected $entities;

    /**
     * Set up dependencies
     */
    public function setUp()
    {
        $this->entities = [
            $this->createMock(EntityInterface::class),
            $this->createMock(EntityInterface::class),
            $this->createMock(EntityInterface::class)
        ];

        $refl = new \ReflectionClass(EntitySet::class);
        $set = $refl->newInstanceWithoutConstructor(); // Contructor wants a real identifiable class

        $this->setPrivateProperty($set, 'entityClass', EntityInterface::class);

        $this->collection = $set->withEntities($this->entities);
    }
}
