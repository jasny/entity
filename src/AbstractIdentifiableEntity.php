<?php

declare(strict_types=1);

namespace Jasny\Entity;

use Jasny\Entity\Traits;

/**
 * All identifiable entity traits.
 */
abstract class AbstractIdentifiableEntity extends AbstractBasicEntity implements IdentifiableEntity
{
    use Traits\IdentifyTrait;
}
