<?php

declare(strict_types=1);

namespace Jasny\Entity;

use Jasny\Entity\Traits;

/**
 * All required traits an entity.
 */
trait BasicEntityTraits
{
    use Traits\AssertGhostTrait;
    use Traits\CompareTrait;
    use Traits\ToAssocTrait;
    use Traits\ToJsonTrait;
    use Traits\FromDataTrait;
    use Traits\SetTrait;
    use Traits\RefreshTrait;
    use Traits\DispatchEventTrait;
}
