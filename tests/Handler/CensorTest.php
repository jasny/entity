<?php

namespace Jasny\Entity\Tests\Handler;

use Jasny\Entity\EntityInterface;
use Jasny\Entity\Handler\Censor;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Handler\Censor
 */
class CensorTest extends TestCase
{
    public function testInvoke()
    {
        $entity = $this->createMock(EntityInterface::class);

        $handler = new Censor(['foo', 'bar', 'qux']);
        $result = $handler($entity, ['foo' => 'hello', 'bar' => 42, 'other' => 'good']);

        $this->assertEquals(['other' => 'good'], $result);
    }

    public function testInvokeWithObject()
    {
        $entity = $this->createMock(EntityInterface::class);

        $handler = new Censor(['foo', 'bar', 'qux']);
        $result = $handler($entity, (object)['foo' => 'hello', 'bar' => 42, 'other' => 'good']);

        $this->assertEquals((object)['other' => 'good'], $result);
    }

    /**
     * @expectedException \TypeError
     */
    public function testInvokeWithNull()
    {
        $entity = $this->createMock(EntityInterface::class);

        $handler = new Censor(['foo', 'bar', 'qux']);
        $handler($entity, null);
    }
}
