<?php

declare(strict_types=1);

namespace Jasny\Entity\Collection;

use Improved as i;
use Jasny\Entity\EntityInterface;

/**
 * An entity collection that works as a map, so with key/value pairs.
 * @see https://en.wikipedia.org/wiki/Associative_array
 *
 * @template TEntity of EntityInterface
 * @extends AbstractCollection<string,TEntity>
 * @implements \ArrayAccess<string,TEntity>
 */
class EntityMap extends AbstractCollection implements \ArrayAccess
{
    /**
     * Create a new collection.
     *
     * @param iterable<EntityInterface> $entities
     * @return void
     */
    protected function setEntities(iterable $entities): void
    {
        $this->entities = i\iterable_to_array($entities, true);
    }


    /**
     * Check if offset exists
     *
     * @param string $index
     * @return bool
     */
    public function offsetExists($index)
    {
        return isset($this->entities[$index]);
    }

    /**
     * Get the entity of a specific index or find entity in set.
     *
     * @param string $index
     * @return EntityInterface
     * @throws \OutOfBoundsException
     *
     * @phpstan-param string $index
     * @phpstan-return TEntity
     */
    public function offsetGet($index)
    {
        if (!isset($this->entities[$index])) {
            throw new \OutOfBoundsException("Key '$index' does not exist in map");
        }

        return $this->entities[$index];
    }

    /**
     * Replace the entity of a specific index
     *
     * @param string          $index
     * @param EntityInterface $entity  Entity or data representation of entity
     * @return void
     *
     * @phpstan-param string  $index
     * @phpstan-param TEntity $entity
     */
    public function offsetSet($index, $entity)
    {
        $this->entities[$index] = i\type_check($entity, $this->getType());
    }

    /**
     * Remove the entity of a specific index
     *
     * @param string $index
     * @return void
     */
    public function offsetUnset($index)
    {
        unset($this->entities[$index]);
    }
}
