<?php

declare(strict_types=1);

namespace Jasny\Entity;

/**
 * All identifiable entity traits.
 */
trait IdentifiableEntityTraits
{
    use EntityTraits;
    use Traits\IdentifyTrait;
    use Traits\LazyLoadingTrait;
}
