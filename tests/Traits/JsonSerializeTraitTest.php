<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\AbstractBasicEntity;
use Jasny\Entity\DynamicEntity;
use Jasny\Entity\Entity;
use Jasny\Entity\BasicEntityTraits;
use Jasny\Entity\Tests\CreateEntityTrait;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \Jasny\Entity\Traits\ToJsonTrait
 */
class JsonSerializeTraitTest extends TestCase
{
    use CreateEntityTrait;

    public function testJsonSerialize()
    {
        $entity = $this->createBasicEntity();
        $entity->foo = 'bar';
        $entity->bar = 22;
        $entity->non_exist = 'zoo';

        $expected = (object)['foo' => 'bar', 'bar' => 22];

        $result = $entity->jsonSerialize();

        $this->assertEquals($expected, $result);
    }

    public function testJsonSerializeDynamic()
    {
        $entity = $this->createDynamicEntity();
        $entity->foo = 'bar';
        $entity->bar = 22;
        $entity->non_exist = 'zoo';

        $expected = (object)['foo' => 'bar', 'bar' => 22, 'non_exist' => 'zoo'];

        $result = $entity->jsonSerialize();

        $this->assertEquals($expected, $result);
    }

    public function testJsonSerializeEvent()
    {
        $entity = $this->createBasicEntity();
        $entity->foo = 'wuz';
        $entity->bar = 22;

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())->method('dispatch')
            ->with($this->isInstanceOf(Event\ToJson::class))
            ->willReturnCallback(function(Event\ToJson $event) {
                $this->assertEquals((object)['foo' => 'wuz', 'bar' => 22], $event->getPayload());
                $event->setPayload((object)['foo' => 'kuz23', 'own' => 'me']);
            });

        $entity->setEventDispatcher($dispatcher);

        $result = $entity->jsonSerialize();

        $this->assertSame((object)['foo' => 'kuz23', 'own' => 'me'], $result);
    }
}
