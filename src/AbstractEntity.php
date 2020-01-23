<?php

declare(strict_types=1);

namespace Jasny\Entity;

use Jasny\Entity\Traits;

/**
 * All required traits an entity.
 */
class AbstractEntity implements EntityInterface
{
    use Traits\CompareTrait;
    use Traits\DispatchEventTrait;
    use Traits\SerializeTrait;
    use Traits\SetTrait;
}
