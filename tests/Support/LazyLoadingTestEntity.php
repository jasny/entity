<?php

namespace Jasny\Support;

use Jasny\Entity\LazyLoadingInterface;
use Jasny\Entity\Traits\JsonSerializeTrait;
use Jasny\Entity\Traits\GetSetTrait;

/**
 * @ignore
 */
class LazyLoadingTestEntity implements \JsonSerializable, LazyLoadingInterface {
    use JsonSerializeTrait;

    public function isGhost()
    {
        return !isset($this->foo);
    }

    public function expand()
    {
        $this->foo = 'bar';
    }

    public static function lazyload($values)
    {

    }

    public function trigger(string $event, $payload = null)
    {
        return $payload;
    }
}
