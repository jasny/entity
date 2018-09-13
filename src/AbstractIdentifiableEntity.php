<?php

declare(strict_types=1);

namespace Jasny\Entity;

use stdClass;
use Jasny\Entity\EntityInterface;
use Jasny\Entity\Traits;

/**
 * Base class for an identifiable entity.
 */
abstract class AbstractIdentifiableEntity extends AbstractBasicEntity implements IdentifiableEntityInterface
{
    use Traits\IdentifyTrait;
    use Traits\LazyLoadingTrait;
}
