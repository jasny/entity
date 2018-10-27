<?php

declare(strict_types=1);

namespace Jasny\Entity;

use stdClass;
use Jasny\Entity\Entity;
use Jasny\Entity\Traits;

/**
 * Base class for an identifiable entity.
 */
abstract class AbstractIdentifiableEntity extends AbstractBasicEntity implements IdentifiableEntity
{
    use Traits\IdentifyTrait;
    use Traits\LazyLoadingTrait;
}
