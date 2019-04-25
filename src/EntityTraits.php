<?php

declare(strict_types=1);

namespace Jasny\Entity;

/**
 * All required traits an entity.
 */
trait EntityTraits
{
    use Traits\SetTrait;
    use Traits\RefreshTrait;
    use Traits\CompareTrait;
    use Traits\ToAssocTrait;
    use Traits\ToJsonTrait;
    use Traits\SetStateTrait;
    use Traits\DispatchEventTrait;
}
