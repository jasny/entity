<?php

declare(strict_types=1);

namespace Jasny\Entity\Handler;

use Jasny\Entity\EntityInterface;

/**
 * Interface for callable objects that can function as Entity trigger handler.
 * These objects must be immutable.
 */
interface HandlerInterface
{
    /**
     * Invoke the handler as callback.
     *
     * @param EntityInterface $entity
     * @param mixed           $data
     * @return mixed
     */
    public function __invoke(EntityInterface $entity, $data = null);
}
