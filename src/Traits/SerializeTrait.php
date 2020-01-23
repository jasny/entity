<?php /** @noinspection PhpPropertyNamingConventionInspection */

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Jasny\Entity\EntityInterface;
use Jasny\Entity\DynamicEntityInterface;
use Jasny\Entity\Event;
use function Jasny\object_get_properties;
use function Jasny\object_set_properties;

/**
 * Serialization and unserialization magic methods.
 */
trait SerializeTrait
{
    /**
     * @var bool
     * @internal
     */
    private $i__new = true;

    /**
     * Mark entity as new or persisted
     *
     * @param bool $state
     * @return void
     */
    final protected function markNew(bool $state): void
    {
        $this->i__new = $state;
    }

    /**
     * Check if the entity is not persisted yet.
     *
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->i__new;
    }

    /**
     * Cast the entity to an associative array.
     *
     * @return array<string,mixed>
     */
    public function __serialize(): array
    {
        $data = object_get_properties($this, $this instanceof DynamicEntityInterface);

        /** @var static&EntityInterface $this */
        return $this->dispatchEvent(new Event\Serialize($this, $data))->getPayload();
    }

    /**
     * Load persisted data into an entity.
     *
     * @param array<string,mixed> $data
     */
    public function __unserialize(array $data): void
    {
        /** @var static&EntityInterface $this */
        $data = $this->dispatchEvent(new Event\Unserialize($this, $data))->getPayload();
        object_set_properties($this, $data, $this instanceof DynamicEntityInterface);

        $this->markNew(false);
    }

    /**
     * Create a new entity and call __unserialize().
     *
     * @param array<string,mixed> $data
     * @return static&EntityInterface
     * @throws \ReflectionException
     */
    final public static function __set_state(array $data)
    {
        $refl = new \ReflectionClass(get_called_class());

        /** @var static&EntityInterface $entity */
        $entity = $refl->newInstanceWithoutConstructor();
        $entity->__unserialize($data);

        return $entity;
    }

    /**
     * Prepare entity for JsonSerialize encoding
     *
     * @return \stdClass
     */
    public function jsonSerialize(): \stdClass
    {
        $data = (object)object_get_properties($this, $this instanceof DynamicEntityInterface);

        /** @var static&EntityInterface $this */
        return $this->dispatchEvent(new Event\ToJson($this, $data))->getPayload();
    }
}
