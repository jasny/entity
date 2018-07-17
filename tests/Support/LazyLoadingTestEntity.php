<?php

namespace Jasny\Support;

use Jasny\Entity\LazyLoadingInterface;
use Jasny\Entity\Traits\JsonSerializeTrait;
use Jasny\Entity\Traits\IdentifyTrait;
use Jasny\Entity\Traits\LazyLoadingTrait;

/**
 * @ignore
 */
class LazyLoadingTestEntity implements \JsonSerializable, LazyLoadingInterface {
    use JsonSerializeTrait;
    use IdentifyTrait;
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
