<?php

declare(strict_types=1);

namespace Jasny\Entity\Tests;

use ArrayIterator;
use Jasny\Entity\AbstractBasicEntity;
use Jasny\Entity\DynamicEntity;
use Jasny\Entity\Entity;
use Jasny\Entity\IdentifiableEntity;
use Jasny\Entity\AbstractIdentifiableEntity;

/**
 * Trait for unit tests to create entities
 */
trait CreateEntityTrait
{
    protected function createBasicEntity(): Entity
    {
        return new class() extends AbstractBasicEntity {
            public $foo;
            public $bar = 0;
            protected $unseen = 3.14;
        };
    }

    protected function createIdentifiableEntity($id): IdentifiableEntity
    {
        return new class($id) extends AbstractIdentifiableEntity {
            public $id;
            public $foo;
            public $bar = 0;
            protected $unseen = 3.14;

            public function __construct($id)
            {
                $this->id = $id;
            }
        };
    }

    protected function createDynamicEntity(): DynamicEntity
    {
        return new class() extends AbstractBasicEntity implements DynamicEntity {
            public $foo;
            public $bar = 0;
            protected $unseen = 3.14;
        };
    }

    protected function createEntityWithConstructor(): Entity
    {
        return new class() extends AbstractBasicEntity {
            public $foo;
            public $bar;
            protected $unseen = 3.14;

            public function __construct()
            {
                $this->bar = 0;
            }
        };
    }

    protected function createNestedEntity(): Entity
    {
        $entity = $this->createBasicEntity();

        $foo = $this->createIdentifiableEntity(42);
        $foo->foo = 'Foo Foo';
        $foo->bar = $entity;

        $uno = $this->createIdentifiableEntity(1);
        $dos = $this->createIdentifiableEntity(2);

        $entity->foo = $foo;
        $entity->bar = (object)[
            'uno' => $uno,
            'dos' => [
                $dos,
                $foo,
            ],
            'tres' => new ArrayIterator([
                'hello',
                $uno,
                $entity,
                'plus',
                $dos,
            ]),
            'more' => [
                'like' => 'this',
                'entity' => $entity,
            ],
        ];

        return $entity;
    }
}
