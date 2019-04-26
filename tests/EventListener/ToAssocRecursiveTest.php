<?php

declare(strict_types=1);

namespace Jasny\Entity\Tests\EventListener;

use ArrayIterator;
use Jasny\Entity\Event;
use Jasny\Entity\EventListener\ToAssocRecursive;
use Jasny\Entity\Tests\CreateEntityTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\EventListener\ToAssocRecursive
 */
class ToAssocRecursiveTest extends TestCase
{
    use CreateEntityTrait;

    public function testToAssocRecursive()
    {
        $entity = $this->createNestedEntity();

        $event = new Event\ToAssoc($entity, $entity->toAssoc());

        $listener = new ToAssocRecursive();
        $listener($event);

        $expected = [
            'foo' => [
                'id' => 42,
                'foo' => 'Foo Foo',
            ],
            'bar' => [
                'uno' => [
                    'id' => 1,
                    'foo' => null,
                    'bar' => 0,
                ],
                'dos' => [
                    [
                        'id' => 2,
                        'foo' => null,
                        'bar' => 0,
                    ],
                    [
                        'id' => 42,
                        'foo' => 'Foo Foo',
                    ],
                ],
                'tres' => [
                    'hello',
                    [
                        'id' => 1,
                        'foo' => null,
                        'bar' => 0,
                    ],
                    null,
                    'plus',
                    [
                        'id' => 2,
                        'foo' => null,
                        'bar' => 0,
                    ],
                ],
                'more' => [
                    'like' => 'this',
                ],
            ],
        ];

        $this->assertEquals($expected, $event->getPayload());
    }
}
