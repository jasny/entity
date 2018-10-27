<?php

namespace Jasny\Entity\Tests\EventHandler;

use Jasny\Entity\Entity;
use Jasny\Entity\EventHandler\Censor;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\EventHandler\Censor
 */
class CensorTest extends TestCase
{
    public function testInvoke()
    {
        $entity = $this->createMock(Entity::class);

        $handler = new Censor(['foo', 'bar', 'qux']);
        $result = $handler($entity, ['foo' => 'hello', 'bar' => 42, 'other' => 'good']);

        $this->assertEquals(['other' => 'good'], $result);
    }

    public function testInvokeWithObject()
    {
        $entity = $this->createMock(Entity::class);

        $handler = new Censor(['foo', 'bar', 'qux']);
        $result = $handler($entity, (object)['foo' => 'hello', 'bar' => 42, 'other' => 'good']);

        $this->assertEquals((object)['other' => 'good'], $result);
    }

    /**
     * @expectedException \TypeError
     */
    public function testInvokeWithNull()
    {
        $entity = $this->createMock(Entity::class);

        $handler = new Censor(['foo', 'bar', 'qux']);
        $handler($entity, null);
    }
}
