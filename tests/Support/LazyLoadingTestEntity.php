<?php

namespace Jasny\Tests\Support;

use Jasny\Entity\EntityInterface;
use Jasny\Entity\Traits\JsonSerializeTrait;
use Jasny\Entity\Traits\IdentifyTrait;
use Jasny\Entity\Traits\LazyLoadingTrait;

/**
 * @ignore
 */
class LazyLoadingTestEntity implements \JsonSerializable, EntityInterface {
    use JsonSerializeTrait;
    use IdentifyTrait;
    use LazyLoadingTrait;

    public $id;
    public $foo = 'bar';

    public function trigger(string $event, $payload = null)
    {
        return $payload;
    }

    public function set($key, $value = null)
    {

    }

    public function toAssoc(): array
    {

    }

    public function isNew(): bool
    {

    }

    public function on(string $event, callable $handler)
    {

    }

    public static function __set_state(array $data)
    {

    }

    public function applyState(array $data)
    {

    }
}
