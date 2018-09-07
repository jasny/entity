<?php

namespace Jasny\Tests\Entity\Traits;

use Jasny\Entity\EntityInterface;
use Jasny\Tests\Support\LazyLoadingTestEntity;
use Jasny\Tests\Support\IdentifyTestEntity;
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
        $entity = new class() {
            use LazyLoadingTrait;

            public static function hasIdProperty(): bool
            {
                return false;
            }

            protected static function getIdProperty(): ?string
            {
                return null;
            }
        };

        $class = get_class($entity);
        $class::lazyload('foo');
    }

    /**
     * Test 'reload' method for non-dynamic entity
     */
    public function testReload()
    {
        $values = ['id' => 'bla', 'foo' => 'kaz', 'bar' => 'bar_dynamic'];

        $entity = $this->createPartialMock(LazyLoadingTestEntity::class, ['trigger']);
        $entity->expects($this->at(0))->method('trigger')->with('before:reload', $values)->willReturn($values);
        $entity->expects($this->at(1))->method('trigger')->with('after:reload');

        $entity->id = 'bla';
        $entity->foo = 'zoo';

        $result = $entity->reload($values);

        $this->assertSame($entity, $result);
        $this->assertSame('bla', $entity->getId());
        $this->assertSame('kaz', $entity->foo);
        $this->assertFalse(isset($entity->bar));
    }

    /**
     * Test 'reload' method for dynamic entity
     */
    public function testReloadDynamic()
    {
        $values = ['id' => 'bla', 'foo' => 'kaz', 'bar' => 'bar_dynamic'];

        $entity = $this->createPartialMock(IdentifyTestEntity::class, ['trigger']);
        $entity->expects($this->at(0))->method('trigger')->with('before:reload', $values)->willReturn($values);
        $entity->expects($this->at(1))->method('trigger')->with('after:reload');

        $entity->id = 'bla';
        $entity->foo = 'zoo';

        $result = $entity->reload($values);

        $this->assertSame($entity, $result);
        $this->assertSame('bla', $entity->getId());
        $this->assertSame('kaz', $entity->foo);
        $this->assertSame('bar_dynamic', $entity->bar);
    }

    /**
     * Test 'reload' method, if entity is not identifiable
     *
     * @expectedException BadMethodCallException
     * @expectedExceptionMessageRegExp /\w+ entity is not identifiable/
     */
    public function testReloadNotIdentifiable()
    {
        $entity = new class() {
            use LazyLoadingTrait;

            public static function hasIdProperty(): bool
            {
                return false;
            }

            protected static function getIdProperty(): ?string
            {
                return null;
            }
        };

        $entity->reload([]);
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
