<?php

namespace Jasny\Tests\EntityCollection;

use PHPUnit\Framework\TestCase;
use Jasny\Entity\EntityInterface;
use Jasny\EntityCollection\EntityList;

/**
 * @covers \Jasny\EntityCollection\EntityList
 * @covers \Jasny\EntityCollection\AbstractEntityCollection
 */
class EntityListTest extends TestCase
{
    public function testCreate()
    {
        $entity1 = $this->createMock(EntityInterface::class);
        $entity2 = $this->createMock(EntityInterface::class);
        $entity3 = $this->createMock(EntityInterface::class);

        $entities = [$entity1, $entity2, 27 => $entity3];

        $list = (new EntityList(EntityInterface::class))
            ->withEntities($entities);

        $this->assertSame([$entity1, $entity2, $entity3], iterator_to_array($list));
    }
}
