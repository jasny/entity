<?php

declare(strict_types=1);

namespace Jasny\Entity\Tests\_Support;

use ArrayIterator;
use Jasny\Entity\AbstractEntity;
use Jasny\Entity\DynamicEntityInterface;
use Jasny\Entity\EntityInterface;
use Jasny\Entity\IdentifiableEntityInterface;
use Jasny\Entity\AbstractIdentifiableEntity;

/**
 * Trait for unit tests to create entities
 */
trait CreateEntityTrait
{
    protected function createBasicEntity(): EntityInterface
    {
        return new class() extends AbstractEntity {
            public $foo;
            public $bar = 0;
            protected $unseen = 3.14;
        };
    }

    protected function createIdentifiableEntity($id): IdentifiableEntityInterface
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

    protected function createDynamicEntity(): DynamicEntityInterface
    {
        return new class() extends AbstractEntity implements DynamicEntityInterface {
            public $foo;
            public $bar = 0;
            protected $unseen = 3.14;
        };
    }

    protected function createEntityWithConstructor(): EntityInterface
    {
        return new class() extends AbstractEntity {
            public $foo;
            public $bar;
            protected $unseen = 3.14;

            public function __construct()
            {
                $this->bar = 0;
            }
        };
    }

    protected function createNestedEntity(): EntityInterface
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
