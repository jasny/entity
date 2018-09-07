<?php

namespace Jasny\Tests\EntityCollection\Traits;

use PHPUnit\Framework\TestCase;
use Jasny\Tests\Support\TestCollectionWithFakeEntityClass;
use Jasny\Entity\Entity;
use Jasny\Entity\EntityInterface;
use Jasny\EntityCollection\AbstractEntityCollection;
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
        $item1 = $this->createPartialMock(Entity::class, []);
        $item2 = $this->createPartialMock(Entity::class, []);
        $item3 = $this->createPartialMock(Entity::class, []);

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
        $source = new class() extends AbstractEntityCollection {
            public $entityClass = EntityInterface::class;
        };

        $collectionClass = get_class($source);

        $collection = $collectionClass::forClass(Entity::class, $iterable);

        $this->assertInstanceOf($collectionClass, $collection);
        $this->assertAttributeEquals($expectedItems, 'entities', $collection);
        $this->assertAttributeEquals(Entity::class, 'entityClass', $collection);
    }

    /**
     * Test 'forClass' method, in case when $class parameter is wrong
     *
     * @expectedException DomainException
     * @expectedExceptionMessageRegExp /Jasny\\Support\\TestCollectionWithFakeEntityClass is only for Foo entities, not Jasny\\Entity\\Entity/
     */
    public function testForClassWrongEntityClass()
    {
        $collection = TestCollectionWithFakeEntityClass::forClass(Entity::class, []);
    }
}
