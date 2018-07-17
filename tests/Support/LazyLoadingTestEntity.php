<?php

namespace Jasny\Support;

use Jasny\Entity\LazyLoadingInterface;
use Jasny\Entity\Traits\JsonSerializeTrait;
use Jasny\Entity\Traits\IdentifiableTrait;
use Jasny\Entity\Traits\LazyLoadingTrait;

/**
 * @ignore
 */
class LazyLoadingTestEntity implements \JsonSerializable, LazyLoadingInterface {
    use JsonSerializeTrait;
    use IdentifiableTrait;
    use LazyLoadingTrait;

    public $id;
    public $foo = 'bar';
    public $isDynamic;

    public function trigger(string $event, $payload = null)
    {
        return $payload;
    }

    public function isDynamic(): bool
    {
        return !!$this->isDynamic;
    }
}
