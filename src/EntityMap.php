<?php

declare(strict_types=1);

namespace Jasny\EntityCollection;

use Jasny\Entity\EntityInterface;
use function Jasny\expect_type;

/**
 * An entity collection that works as a map, so a key/value pairs.
 * @see https://en.wikipedia.org/wiki/Associative_array
 */
class EntityMap extends AbstractEntityCollection implements EntityMapInterface
{
    /**
     * Create a new collection.
     *
     * @param EntityInterface[]|iterable $entities  Array of entities
     * @return void
     */
    protected function setEntities(iterable $entities): void
    {
        $this->entities = [];
        $entityClass = $this->getEntityClass();

        foreach ($entities as $key => $entity) {
            expect_type($entity, $entityClass, "Expected {$entityClass} for item '{$key}', %s given");
            $this->entities[$key] = $entity;
        }
    }


    /**
     * Check if offset exists
     *
     * @param string $index
     * @return bool
     */
    public function offsetExists($index)
    {
        expect_type($index, 'string');

        return isset($this->entities[$index]);
    }

    /**
     * Get the entity of a specific index or find entity in set
     *
     * @param string $index
     * @return EntityInterface
     * @throws \OutOfBoundsException
     */
    public function offsetGet($index)
    {
        expect_type($index, 'string', \InvalidArgumentException::class, 'Key must be a string, %s given');

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
     */
    public function offsetSet($index, $entity)
    {
        expect_type($index, 'string', \InvalidArgumentException::class, 'Key must be a string, %s given');
        expect_type($entity, $this->getEntityClass());

        $this->entities[$index] = $entity;
    }

    /**
     * Remove the entity of a specific index
     *
     * @param string $index
     * @return void
     */
    public function offsetUnset($index)
    {
        expect_type($index, 'string', \InvalidArgumentException::class, 'Key must be a string, %s given');
        unset($this->entities[$index]);
    }
}
