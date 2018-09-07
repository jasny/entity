<?php

namespace Jasny\Tests\EntityCollection;

use PHPUnit\Framework\TestCase;
use Jasny\Entity\EntityInterface;
use Jasny\EntityCollection\EntityList;

/**
 * @covers Jasny\EntityCollection\EntityList
 */
class EntityListTest extends TestCase
{
    /**
     * Test creating list
     */
    public function testCreate()
    {
        $entity1 = $this->createMock(EntityInterface::class);
        $entity2 = $this->createMock(EntityInterface::class);
        $entity3 = $this->createMock(EntityInterface::class);

        $entities = [1 => $entity1, 3 => $entity2, 7 => $entity3];
        $expected = [$entity1, $entity2, $entity3];

        $list = EntityList::forClass(EntityInterface::class, $entities);

        $this->assertAttributeSame($expected, 'entities', $list);
    }
}
