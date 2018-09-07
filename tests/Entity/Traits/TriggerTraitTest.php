<?php

namespace Jasny\Tests\Entity\Traits;

use Jasny\Tests\Support\TestEntity;
use PHPUnit\Framework\TestCase;

/**
 * @covers Jasny\Entity\Traits\TriggerTrait
 * @group entity
 */
class TriggerTraitTest extends TestCase
{
    /**
     * Test setting and getting triggers
     */
    public function testTriggers()
    {
        $values = ['foo' => 'bar'];
        $processedValues1 = ['foo' => 'zoo'];
        $processedValues2 = ['foo' => 'bla'];

        $entity = $this->createPartialMock(TestEntity::class, []);
        $tester = $this;
        $count = (object)['value' => 0];

        $callback1 = function($entityParam, $data) use ($entity, $values, $count, $processedValues1, $tester) {
            $tester->assertSame($entity, $entityParam);
            $tester->assertSame($values, $data);
            $count->value++;

            return $processedValues1;
        };

        $callback2 = function($entityParam, $data) use ($entity, $processedValues1, $processedValues2, $count, $tester) {
            $tester->assertSame($entity, $entityParam);
            $tester->assertSame($processedValues1, $data);
            $count->value++;

            return $processedValues2;
        };

        $callbackNotCalled = function($entityParam, $data) use ($count) {
            $count->value += 10;
        };

        $entity->on('foo_event', $callback1);
        $entity->on('foo_event', $callback2);
        $entity->on('bar_event', $callbackNotCalled);

        $result = $entity->trigger('foo_event', $values);

        $this->assertSame($processedValues2, $result);
        $this->assertSame(2, $count->value);
    }

    /**
     * Test 'trigger' method, in case when there are no triggers set
     */
    public function testTriggerNoCallbacks()
    {
        $values = ['foo' => 'bar'];
        $entity = $this->createPartialMock(TestEntity::class, []);

        $result = $entity->trigger('foo_event', $values);

        $this->assertSame($values, $result);
    }
}
