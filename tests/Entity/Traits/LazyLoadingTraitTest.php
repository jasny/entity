<?php

namespace Jasny\Tests\Entity\Traits;

use Jasny\Entity\EntityInterface;
use Jasny\Entity\Traits\LazyLoadingTrait;
use Jasny\Entity\Traits\IdentifyTrait;
use Jasny\Entity\Traits\TriggerTrait;
use Jasny\Entity\DynamicInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers Jasny\Entity\Traits\LazyLoadingTrait
 * @group entity
 */
class LazyLoadingTraitTest extends TestCase
{
    /**
     * Test 'isGhost' method
     */
    public function testIsGhost()
    {
        $entity = $this->getMockForTrait(LazyLoadingTrait::class);
        $result = $entity->isGhost();

        $this->assertFalse($result);
    }

    /**
     * Test 'lazyload' method
     */
    public function testLazyload()
    {
        $source = new class() {
            use LazyLoadingTrait, IdentifyTrait;

            public $id;

            public function trigger(string $event, $payload = null)
            {

            }
        };

        $class = get_class($source);

        $entity = $class::lazyload('foo');

        $this->assertInstanceOf($class, $entity);
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
            use LazyLoadingTrait, IdentifyTrait;

            public function trigger(string $event, $payload = null)
            {

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

        $entity = new class() {
            use LazyLoadingTrait, IdentifyTrait;

            public $id;
            public $foo;
            public $triggerEvents = [];

            public function trigger(string $event, $payload = null)
            {
                $this->triggerEvents[] = $event;

                if ($event === 'before:reload') {
                    return $payload;
                }
            }
        };

        $entity->id = 'bla';
        $entity->foo = 'zoo';

        $result = $entity->reload($values);

        $this->assertSame($entity, $result);
        $this->assertSame('bla', $entity->getId());
        $this->assertSame('kaz', $entity->foo);
        $this->assertFalse(isset($entity->bar));
        $this->assertSame(['before:reload', 'after:reload'], $entity->triggerEvents);
    }

    /**
     * Test 'reload' method for dynamic entity
     */
    public function testReloadDynamic()
    {
        $values = ['id' => 'bla', 'foo' => 'kaz', 'bar' => 'bar_dynamic'];

        $entity = new class() implements DynamicInterface {
            use LazyLoadingTrait, IdentifyTrait;

            public $id;
            public $foo;
            public $triggerEvents = [];

            public function trigger(string $event, $payload = null)
            {
                $this->triggerEvents[] = $event;

                if ($event === 'before:reload') {
                    return $payload;
                }
            }
        };

        $entity->id = 'bla';
        $entity->foo = 'zoo';

        $result = $entity->reload($values);

        $this->assertSame($entity, $result);
        $this->assertSame('bla', $entity->getId());
        $this->assertSame('kaz', $entity->foo);
        $this->assertSame('bar_dynamic', $entity->bar);
        $this->assertSame(['before:reload', 'after:reload'], $entity->triggerEvents);
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
            use LazyLoadingTrait, IdentifyTrait;

            public function trigger(string $event, $payload = null)
            {

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

        $entity = new class() {
            use LazyLoadingTrait, IdentifyTrait, TriggerTrait;

            public $id;
            public $foo;
            public $triggerEvents = [];
        };

        $entity->id = 'bla';
        $entity->foo = 'zoo';

        $result = $entity->reload($values);
    }
}
