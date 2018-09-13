<?php

declare(strict_types=1);

namespace Jasny\Entity;

/**
 * Identifiable entity
 */
interface IdentifiableEntityInterface extends EntityInterface
{
    /**
     * Get entity id.
     *
     * @return mixed
     */
    public function getId();


    /**
     * Check if the object is a ghost.
     *
     * @return bool
     */
    public function isGhost(): bool;

    /**
     * Lazy load an entity, only the id is known.
     *
     * @param mixed $id
     * @return static
     */
    public static function lazyload($id);
}
