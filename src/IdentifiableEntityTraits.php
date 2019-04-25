<?php

declare(strict_types=1);

namespace Jasny\Entity;

use Jasny\Entity\Traits;

/**
 * All identifiable entity traits.
 */
trait IdentifiableEntityTraits
{
    use BasicEntityTraits;
    use Traits\IdentifyTrait;
    use Traits\LazyLoadingTrait;
}
