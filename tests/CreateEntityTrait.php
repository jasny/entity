<?php

declare(strict_types=1);

namespace Jasny\Entity\Tests;

use Jasny\Entity\BasicEntityTraits;
use Jasny\Entity\DynamicEntity;
use Jasny\Entity\Entity;
use Jasny\Entity\IdentifiableEntity;
use Jasny\Entity\IdentifiableEntityTraits;

/**
 * Trait for unit tests to create entities
 */
trait CreateEntityTrait
{
    protected function createBasicEntity(): Entity
    {
        return new class() implements Entity {
            use BasicEntityTraits;

            public $foo;
            public $bar = 0;
        };
    }

    protected function createIdentifiableEntity($id): IdentifiableEntity
    {
        return new class($id) implements IdentifiableEntity {
            use IdentifiableEntityTraits;

            public $id;
            public $foo;
            public $bar = 0;

            public function __construct($id)
            {
                $this->id = $id;
            }
        };
    }

    protected function createDynamicEntity(): DynamicEntity
    {
        return new class() implements DynamicEntity {
            use BasicEntityTraits;

            public $foo;
            public $bar = 0;
        };
    }

    protected function createEntityWithConstructor(): Entity
    {
        return new class() implements Entity {
            use BasicEntityTraits;

            public $foo;
            public $bar;

            public function __construct()
            {
                $this->num = 0;
            }
        };
    }

}
