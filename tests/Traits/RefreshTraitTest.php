<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\BasicEntityTraits;
use Jasny\Entity\Event\AfterRefresh;
use Jasny\Entity\Event\BeforeRefresh;
use Jasny\Entity\IdentifiableEntityTraits;
use Jasny\Entity\DynamicEntity;
use Jasny\Entity\Entity;
use Jasny\Entity\IdentifiableEntity;
use Jasny\Entity\Tests\CreateEntityTrait;
use Jasny\Entity\Traits\FromDataTrait;
use Jasny\TestHelper;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \Jasny\Entity\Traits\FromDataTrait
 */
class RefreshTraitTest extends TestCase
{
    use TestHelper;
    use CreateEntityTrait;

    /**
     * Test 'refresh' method
     */
    public function testRefresh()
    {
        $entity = $this->createBasicEntity();
        $entity->foo = 'loo';
        $entity->bar = 22;

        $replacement = clone $entity;
        $replacement->foo = 'kazan';
        $replacement->bar = 99;
        $replacement->dyn = 'dynamic';

        $entity->refresh($replacement);

        $this->assertEquals('kazan', $entity->foo);
        $this->assertEquals(99, $entity->bar);
        $this->assertObjectNotHasAttribute('dyn', $entity);
    }

    /**
     * Test 'refresh' method for dynamic entity
     */
    public function testRefreshDynamic()
    {
        $entity = $this->createDynamicEntity();
        $entity->foo = 'loo';
        $entity->bar = 22;

        $replacement = clone $entity;
        $replacement->foo = 'kazan';
        $replacement->bar = 99;
        $replacement->dyn = 'dynamic';

        $entity->refresh($replacement);

        $this->assertEquals('kazan', $entity->foo);
        $this->assertEquals(99, $entity->bar);
        $this->assertEquals('dynamic', $entity->dyn);
    }

    /**
     * Test events for 'refresh' method
     */
    public function testRefreshEvent()
    {
        $entity = $this->createBasicEntity();
        $entity->foo = 'loo';
        $entity->bar = 22;

        $replacement = clone $entity;
        $replacement->foo = 'kazan';
        $replacement->bar = 99;
        $replacement->dyn = 'dynamic';

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [new BeforeRefresh($entity, $replacement)],
                [new AfterRefresh($entity, ['foo' => 'kazan', 'bar' => 99])]
            );

        $entity->setEventDispatcher($dispatcher);

        $entity->refresh($replacement);

        $this->assertEquals('kazan', $entity->foo);
        $this->assertEquals(99, $entity->bar);
        $this->assertObjectNotHasAttribute('dyn', $entity);
    }

    /**
     * Test events for 'refresh' method, modifying the data in the event listener.
     */
    public function testRefreshEventModifyData()
    {
        $entity = $this->createBasicEntity();
        $entity->foo = 'loo';
        $entity->bar = 22;

        $replacement = clone $entity;
        $replacement->foo = 'kazan';
        $replacement->bar = 99;
        $replacement->dyn = 'dynamic';

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$this->isInstanceOf(BeforeRefresh::class)],
                [$this->isInstanceOf(AfterRefresh::class)]
            )
            ->willReturnCallback(function($event) use ($entity, $replacement) {
                $this->assertSame($entity, $event->getEntity());

                if ($event instanceof BeforeRefresh) {
                    $this->assertSame($replacement, $event->getPayload());
                    $event->setPayload(['foo' => 'kaz', 'dyn' => 'wut']); // 'bar' remains unchanged
                }

                if ($event instanceof AfterRefresh) {
                    $this->assertEquals(['foo' => 'kaz', 'dyn' => 'wut'], $event->getPayload());
                }
            });

        $entity->setEventDispatcher($dispatcher);

        $entity->refresh($replacement);

        $this->assertEquals('kaz', $entity->foo);
        $this->assertEquals(22, $entity->bar);
        $this->assertObjectNotHasAttribute('loo', $entity);
    }

    /**
     * Test 'refresh' method for identifiable entity
     */
    public function testRefreshIdentifiable()
    {
        $entity = $this->createIdentifiableEntity('one');
        $entity->foo = 'loo';

        $replacement = clone $entity;
        $replacement->id = 'one';
        $replacement->foo = 'kazan';

        $entity->refresh($replacement);

        $this->assertEquals('one', $entity->id);
        $this->assertEquals('kazan', $entity->foo);
        $this->assertObjectNotHasAttribute('loo', $entity);
    }

    /**
     * Test 'refresh' method, if refresh values have wrong id
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /Replacement .+? is not the same entity; id "one" doesn't match "two"/
     */
    public function testRefreshWrongId()
    {
        $entity = $this->createIdentifiableEntity('one');
        $entity->foo = 'loo';
        $entity->bar = 22;

        $replacement = clone $entity;
        $entity->id = 'two';
        $replacement->foo = 'kazan';

        $entity->refresh($replacement);
    }

    /**
     * Test 'refresh' method, if refresh values have wrong id
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage id {"key":"one","v":42} doesn't match {"key":"one","v":36}
     */
    public function testRefreshWrongCompositeId()
    {
        $entity = $this->createIdentifiableEntity('one');
        $entity->id = ['key' => 'one', 'v' => 42];
        $entity->foo = 'loo';
        $entity->bar = 22;

        $replacement = clone $entity;
        $entity->id = ['key' => 'one', 'v' => 36];
        $replacement->foo = 'kazan';

        $entity->refresh($replacement);
    }
}
