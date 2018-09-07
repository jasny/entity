<?php

namespace Jasny\Tests\EntityCollection;

use PHPUnit\Framework\TestCase;
use Jasny\Tests\Support\IdentifyTestEntity;

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
        $entity1 = $this->createMock(IdentifyTestEntity::class);
        $entity2 = $this->createMock(IdentifyTestEntity::class);
        $entity3 = $this->createMock(IdentifyTestEntity::class);

        $entities = [1 => $entity1, 3 => $entity2, 7 => $entity3];
        $expected = [$entity1, $entity2, $entity3];

        $map = EntityList::forClass(IdentifyTestEntity::class, $entities);

        $this->assertAttributeSame($expected, 'entities', $map);
    }
}
