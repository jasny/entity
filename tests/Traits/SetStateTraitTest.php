<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\EntityTraits;
use Jasny\Entity\IdentifiableEntityTraits;
use Jasny\Entity\DynamicEntity;
use Jasny\Entity\Entity;
use Jasny\Entity\IdentifiableEntity;
use Jasny\Entity\Traits\SetStateTrait;
use Jasny\TestHelper;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Traits\SetStateTrait
 */
class SetStateTraitTest extends TestCase
{
    use TestHelper;

    protected function createObject(): Entity
    {
        return new class() implements Entity {
            use EntityTraits;
            public $foo;
            public $num = 0;
        };
    }

    protected function createDynamicObject(): DynamicEntity
    {
        return new class() implements DynamicEntity {
            use EntityTraits;
            public $foo;
            public $num = 0;
        };
    }

    protected function createObjectWithConstructor(): Entity
    {
        return new class() implements Entity {
            use EntityTraits;
            public $foo;
            public $num;

            public function __construct()
            {
                $this->num = 0;
            }
        };
    }

    protected function createObjectWithTrigger(): Entity
    {
        return new class() implements Entity {
            use EntityTraits;

            public $foo;
            public $num = 0;
            protected $trigger;

            public function setTrigger(callable $trigger): void {
                $this->trigger = $trigger;
            }

            public function trigger(string $event, $payload = null) {
                return call_user_func($this->trigger, $event, $payload);
            }
        };
    }

    protected function createIdentifiableObject(): IdentifiableEntity
    {
        return new class() implements IdentifiableEntity {
            use IdentifiableEntityTraits;

            public $id;
            public $foo;
            public $num;

            public function __construct()
            {
                $this->num = 0;
            }
        };
    }


    /**
     * Test '__set_state' method for non-dynamic entity
     */
    public function testSetState()
    {
        $source = $this->createObject();
        $class = get_class($source);

        $entity = $class::__set_state(['foo' => 'bar', 'num' => 22, 'dyn' => 'woof']);

        $this->assertInstanceOf($class, $entity);

        $this->assertAttributeSame('bar', 'foo', $entity);
        $this->assertAttributeSame(22, 'num', $entity);

        $this->assertObjectNotHasAttribute('dyn', $entity);
    }

    /**
     * Test '__set_state' method for dynamic entity
     */
    public function testSetStateWithDynamicEntity()
    {
        $source = $this->createDynamicObject();
        $class = get_class($source);

        $entity = $class::__set_state(['foo' => 'bar', 'num' => 22, 'dyn' => 'woof']);

        $this->assertInstanceOf($class, $entity);
        $this->assertAttributeSame('bar', 'foo', $entity);
        $this->assertAttributeSame(22, 'num', $entity);
        $this->assertAttributeSame('woof', 'dyn', $entity);
    }

    /**
     * Test '__set_state' method when constructor should be called
     */
    public function testSetStateConstruct()
    {
        $source = $this->createObjectWithConstructor();
        $class = get_class($source);

        $entity = $class::__set_state([]);

        $this->assertInstanceOf($class, $entity);
        $this->assertAttributeSame(0, 'num', $entity);
    }

    /**
     * Test 'markAsPersisted' method
     */
    public function testMarkAsPersisted()
    {
        $entity = $this->getMockForTrait(SetStateTrait::class);
        $entity->expects($this->once())->method('trigger')->with('persisted');

        $result = $entity->markAsPersisted();

        $this->assertSame($entity, $result);
        $this->assertFalse($entity->isNew());
    }


    /**
     * Test 'refresh' method
     */
    public function testRefresh()
    {
        $entity = $this->createObject();
        $entity->foo = 'bar';
        $entity->num = 22;

        $replacement = clone $entity;
        $replacement->foo = 'kazan';
        $replacement->num = 99;
        $replacement->bar = 'dynamic';

        $entity->refresh($replacement);

        $this->assertAttributeEquals('kazan', 'foo', $entity);
        $this->assertAttributeEquals(99, 'num', $entity);
        $this->assertObjectNotHasAttribute('bar', $entity);
    }

    /**
     * Test 'refresh' method for dynamic entity
     */
    public function testRefreshDynamic()
    {
        $entity = $this->createDynamicObject();
        $entity->foo = 'bar';
        $entity->num = 22;

        $replacement = clone $entity;
        $replacement->foo = 'kazan';
        $replacement->num = 99;
        $replacement->bar = 'dynamic';

        $entity->refresh($replacement);

        $this->assertAttributeEquals('kazan', 'foo', $entity);
        $this->assertAttributeEquals(99, 'num', $entity);
        $this->assertAttributeEquals('dynamic', 'bar', $entity);
    }

    /**
     * Test trigger for 'refresh' method
     */
    public function testRefreshTrigger()
    {
        $entity = $this->createObjectWithTrigger();
        $entity->foo = 'bar';
        $entity->num = 22;

        $replacement = clone $entity;
        $replacement->foo = 'kazan';
        $replacement->num = 99;
        $replacement->bar = 'dynamic';

        $data = ['foo' => 'kaz', 'bar' => 'dyn']; // num remains unchanged

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

        $this->assertAttributeEquals('kaz', 'foo', $entity);
        $this->assertAttributeEquals(22, 'num', $entity);
        $this->assertObjectNotHasAttribute('bar', $entity);
    }

    /**
     * Test 'refresh' method for identifiable entity
     */
    public function testRefreshIdentifiable()
    {
        $entity = $this->createIdentifiableObject();
        $entity->id = 'one';
        $entity->foo = 'bar';

        $replacement = clone $entity;
        $replacement->id = 'one';
        $replacement->foo = 'kazan';

        $entity->refresh($replacement);

        $this->assertAttributeEquals('one', 'id', $entity);
        $this->assertAttributeEquals('kazan', 'foo', $entity);
        $this->assertObjectNotHasAttribute('bar', $entity);
    }

    /**
     * Test 'refresh' method, if refresh values have wrong id
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /Replacement .+? is not the same entity; id "one" doesn't match "two"/
     */
    public function testRefreshWrongId()
    {
        $entity = $this->createIdentifiableObject();
        $entity->id = 'one';
        $entity->foo = 'bar';
        $entity->num = 22;

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
        $entity = $this->createIdentifiableObject();
        $entity->id = ['key' => 'one', 'v' => 42];
        $entity->foo = 'bar';
        $entity->num = 22;

        $replacement = clone $entity;
        $entity->id = ['key' => 'one', 'v' => 36];
        $replacement->foo = 'kazan';

        $entity->refresh($replacement);
    }
}
