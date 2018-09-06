<?php

declare(strict_types=1);

namespace Jasny\Entity\Handler;

use Jasny\EntityInterface;

/**
 * Interface for callable objects that can function as Entity trigger handler.
 * These objects must be immutable.
 */
interface HandlerInterface
{
    /**
     * Invoke the handler as callback
     *
     * @param EntityInterface $entity
     * @param array|stdClass  $data
     * @return array|stdClass
     */
    public function __invoke(EntityInterface $entity, $data = null);
}
