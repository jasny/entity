<?php

declare(strict_types=1);

namespace Jasny\Entity;

use Jasny\Entity\Traits;

/**
 * All required traits an entity.
 */
abstract class AbstractBasicEntity implements Entity
{
    use Traits\CompareTrait;
    use Traits\ToAssocTrait;
    use Traits\ToJsonTrait;
    use Traits\FromDataTrait;
    use Traits\SetTrait;
    use Traits\DispatchEventTrait;
    use Traits\EventListenerTrait;
}
