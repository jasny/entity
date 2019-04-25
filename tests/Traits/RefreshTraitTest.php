<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\BasicEntityTraits;
use Jasny\Entity\IdentifiableEntityTraits;
use Jasny\Entity\DynamicEntity;
use Jasny\Entity\Entity;
use Jasny\Entity\IdentifiableEntity;
use Jasny\Entity\Tests\CreateEntityTrait;
use Jasny\Entity\Traits\FromDataTrait;
use Jasny\TestHelper;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\TestCase;

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
     * Test trigger for 'refresh' method
     */
    public function testRefreshTrigger()
    {
        $this->markTestIncomplete();

        $entity = $this->createBasicEntityWithTrigger();
        $entity->foo = 'loo';
        $entity->bar = 22;

        $replacement = clone $entity;
        $replacement->foo = 'kazan';
        $replacement->bar = 99;
        $replacement->bar = 'dynamic';

        $data = ['foo' => 'kaz', 'loo' => 'dyn']; // bar remains unchanged

        $trigger = $this->createCallbackMock(
            $this->atLeast(2),
            function(InvocationMocker $invoke) use ($replacement, $data) {
                $invoke->withConsecutive(
                    ['refresh.before', $this->identicalTo($replacement)],
                    ['refresh.after', $this->identicalTo($data)],
                    [$this->anything()]
                );
                $invoke->willReturnOnConsecutiveCalls($data, null);
            }
        );
        $entity->setTrigger($trigger);

        $noTrigger = $this->createCallbackMock($this->any(), function(InvocationMocker $invoke) {
            $invoke->with($this->logicalNot($this->stringStartsWith('refresh')));
            $invoke->willReturn(null);
        });
        $replacement->setTrigger($noTrigger);

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
