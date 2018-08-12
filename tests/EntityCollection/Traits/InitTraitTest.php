<?php

namespace Jasny\EntityCollection;

use PHPUnit\Framework\TestCase;
use Jasny\Support\IdentifyTestEntity;
use Jasny\Support\TestCollectionWithFakeEntityClass;
use Jasny\Entity;
use Jasny\EntityCollection\Traits\InitTrait;

/**
 * @covers Jasny\EntityCollection\Traits\InitTrait
 * @group collection
 */
class InitTraitTest extends TestCase
{
    /**
     * Provide data for testing 'forClass' method
     *
     * @return array
     */
    public function forClassProvider()
    {
        $item1 = $this->createPartialMock(IdentifyTestEntity::class, []);
        $item2 = $this->createPartialMock(IdentifyTestEntity::class, []);
        $item3 = $this->createPartialMock(IdentifyTestEntity::class, []);

        $item1->id = 'a';
        $item2->id = 'b';
        $item3->id = 'c';

        $array = [$item1, $item2, $item3];

        return [
            [[], 0, []],
            [new \ArrayObject([]), 0, []],
            [$array, 3, $array],
            [new \ArrayObject($array), 3, $array],
        ];
    }

    /**
     * Test 'forClass' method
     *
     * @dataProvider forClassProvider
     */
    public function testForClass(iterable $iterable, $expectedCount, $expectedItems)
    {
        $class = IdentifyTestEntity::class;
        $collection = AbstractEntityCollection::forClass($class, $iterable);

        $this->assertInstanceOf(AbstractEntityCollection::class, $collection);
        $this->assertAttributeEquals($expectedItems, 'entities', $collection);
        $this->assertAttributeEquals($class, 'entityClass', $collection);
    }

    /**
     * Test 'forClass' method, in case when $class parameter is wrong
     *
     * @expectedException DomainException
     * @expectedExceptionMessageRegExp /Jasny\\Support\\TestCollectionWithFakeEntityClass is only for Foo entities, not Jasny\\Entity/
     */
    public function testForClassWrongEntityClass()
    {
        $collection = TestCollectionWithFakeEntityClass::forClass(Entity::class, []);
    }
}
