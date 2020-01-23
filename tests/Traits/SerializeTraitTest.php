<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\Event;
use Jasny\Entity\Tests\_Support\CreateEntityTrait;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \Jasny\Entity\Traits\SerializeTrait
 */
class SerializeTraitTest extends TestCase
{
    use CreateEntityTrait;

    public function testSerialize()
    {
        $entity = $this->createBasicEntity();

        $entity->foo = 'wuz';
        $entity->bar = 23;
        $entity->dyn = 'woof'; // ignored

        $result = $entity->__serialize();

        $this->assertSame(['foo' => 'wuz', 'bar' => 23], $result);
        $this->assertTrue($entity->isNew());
    }

    public function testSerializeWithDynamicEntity()
    {
        $entity = $this->createDynamicEntity();
        $entity->foo = 'wuz';
        $entity->bar = 23;
        $entity->dyn = 'woof';

        $result = $entity->__serialize();

        $this->assertSame(['foo' => 'wuz', 'bar' => 23, 'dyn' => 'woof'], $result);
    }

    public function testSerializeEvent()
    {
        $entity = $this->createBasicEntity();
        $entity->foo = 'wuz';
        $entity->bar = 23;

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())->method('dispatch')
            ->with($this->isInstanceOf(Event\Serialize::class))
            ->willReturnCallback(function (Event\Serialize $event) {
                $this->assertEquals(['foo' => 'wuz', 'bar' => 23], $event->getPayload());
                $event->setPayload(['foo' => 'kuz23', 'own' => 'me']);
            });

        $entity->setEventDispatcher($dispatcher);

        $result = $entity->__serialize();

        $this->assertSame(['foo' => 'kuz23', 'own' => 'me'], $result);
    }

    public function testUnserialize()
    {
        $entity = $this->createBasicEntity();
        $this->assertTrue($entity->isNew());

        $entity->__unserialize(['foo' => 'loo', 'bar' => 22, 'dyn' => 'woof']);
        $this->assertFalse($entity->isNew());

        $this->assertEquals('loo', $entity->foo);
        $this->assertEquals(22, $entity->bar);

        $this->assertObjectNotHasAttribute('dyn', $entity);
    }

    public function testUnserializeWithDynamicEntity()
    {
        $entity = $this->createDynamicEntity();
        $entity->__unserialize(['foo' => 'loo', 'bar' => 22, 'dyn' => 'woof']);

        $this->assertEquals('loo', $entity->foo);
        $this->assertEquals(22, $entity->bar);

        $this->assertObjectHasAttribute('dyn', $entity);
        $this->assertEquals('woof', $entity->dyn);
    }


    public function testSetState()
    {
        $source = $this->createBasicEntity();
        $entity = $source::__set_state(['foo' => 'loo', 'bar' => 22, 'dyn' => 'woof']);

        $this->assertInstanceOf(get_class($source), $entity);
        $this->assertFalse($entity->isNew());

        $this->assertEquals('loo', $entity->foo);
        $this->assertEquals(22, $entity->bar);

        $this->assertObjectNotHasAttribute('dyn', $entity);
    }

    public function testSetStateWithDynamicEntity()
    {
        $source = $this->createDynamicEntity();
        $entity = $source::__set_state(['foo' => 'loo', 'bar' => 22, 'dyn' => 'woof']);

        $this->assertInstanceOf(get_class($source), $entity);
        $this->assertEquals('loo', $entity->foo);
        $this->assertEquals(22, $entity->bar);

        $this->assertObjectHasAttribute('dyn', $entity);
        $this->assertEquals('woof', $entity->dyn);
    }

    public function testSetStateDoesntCallConstructor()
    {
        $source = $this->createEntityWithConstructor();
        $entity = $source::__set_state([]);

        $this->assertInstanceOf(get_class($source), $entity);
        $this->assertNull($entity->bar);
    }

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
            ->willReturnCallback(function (Event\ToJson $event) {
                $this->assertEquals((object)['foo' => 'wuz', 'bar' => 22], $event->getPayload());
                $event->setPayload((object)['foo' => 'kuz23', 'own' => 'me']);
            });

        $entity->setEventDispatcher($dispatcher);

        $result = $entity->jsonSerialize();

        $this->assertEquals((object)['foo' => 'kuz23', 'own' => 'me'], $result);
    }
}
