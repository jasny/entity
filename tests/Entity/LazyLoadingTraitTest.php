<?php

namespace Jasny\Entity;

use Jasny\Support\LazyLoadingTestEntity;
use Jasny\Support\DynamicTestEntity;
use Jasny\Entity\Traits\LazyLoadingTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers Jasny\Entity\Traits\LazyLoadingTrait
 * @group entity
 */
class LazyLoadingTraitTest extends TestCase
{
    public function setUp()
    {
        $this->entity = $this->createPartialMock(LazyLoadingTestEntity::class, []);
    }

    /**
     * Test 'isGhost' method
     */
    public function testIsGhost()
    {
        $result = $this->entity->isGhost();

        $this->assertFalse($result);
    }

    /**
     * Test 'lazyload' method
     */
    public function testLazyload()
    {
        $entity = LazyLoadingTestEntity::lazyload('foo');

        $this->assertInstanceOf(LazyLoadingTestEntity::class, $entity);
        $this->assertSame('foo', $entity->getId());
        $this->assertFalse(isset($entity->foo));
        $this->assertTrue($entity->isGhost());
    }

    /**
     * Test 'lazyload' method, if entity is not Identifiable
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessageRegExp /\w+ entity is not identifiable/
     */
    public function testLazyloadException()
    {
        $entity = DynamicTestEntity::lazyload('foo');
    }

    /**
     * Provide data for testing 'reload' method
     *
     * @return array
     */
    public function reloadProvider()
    {
        return [
            [true, 'bar_dynamic'],
            [false, null]
        ];
    }

    /**
     * Test 'reload' method
     *
     * @dataProvider reloadProvider
     */
    public function testReload($isDynamic, $expectedBar)
    {
        $values = ['id' => 'bla', 'foo' => 'kaz', 'bar' => 'bar_dynamic'];

        $entity = $this->createPartialMock(LazyLoadingTestEntity::class, ['trigger']);
        $entity->expects($this->at(0))->method('trigger')->with('before:reload', $values)->willReturn($values);
        $entity->expects($this->at(1))->method('trigger')->with('after:reload');

        $entity->id = 'bla';
        $entity->foo = 'zoo';
        $entity->isDynamic = $isDynamic;

        $result = $entity->reload($values);

        $this->assertSame($entity, $result);
        $this->assertSame('bla', $entity->getId());
        $this->assertSame('kaz', $entity->foo);

        $expectedBar ?
            $this->assertSame($expectedBar, $entity->bar) :
            $this->assertFalse(isset($entity->bar));
    }

    /**
     * Test 'reload' method, if entity is not identifiable
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessageRegExp /\w+ entity is not identifiable/
     */
    public function testReloadNotIdentifiable()
    {
        $entity = $this->createPartialMock(DynamicTestEntity::class, ['trigger']);
        $entity->expects($this->never())->method('trigger');

        $result = $entity->reload([]);
    }

    /**
     * Test 'reload' method, if reload values have wrong id
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegExp /Id in reload data doesn't match entity id/
     */
    public function testReloadWrongId()
    {
        $values = ['id' => 'wrong_id', 'foo' => 'kaz'];

        $entity = $this->createPartialMock(LazyLoadingTestEntity::class, ['trigger']);
        $entity->expects($this->once())->method('trigger')->with('before:reload', $values)->willReturn($values);

        $entity->id = 'bla';
        $entity->foo = 'zoo';

        $result = $entity->reload($values);
    }
}
