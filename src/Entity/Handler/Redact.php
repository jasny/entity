<?php


namespace Jasny\Entity\Handler;

use Jasny\EntityInterface;
use InvalidArgumentException;
use stdClass;

/**
 * Redact an entity, not returning values when cast to values.
 * Immutable object `with` and `only` methods return a new object.
 */
class Redact
{
    /**
     * @var boolean
     */
    protected $default = false;

    /**
     * @var array
     */
    protected $censored = [];


    /**
     * Get if properties are censored by default.
     *
     * @return boolean
     */
    public function isCensoredByDefault()
    {
        return $this->default;
    }

    /**
     * Check if the property is censored/
     *
     * @param string $property
     * @return boolean
     */
    public function hasCensored($property)
    {
        return $this->censored[$property] ?? $this->default;
    }

    /**
     * Censor properties.
     *
     * @param string[] $properties
     * @return static
     */
    public function without(...$properties): self
    {
        $clone = clone $this;

        foreach ($properties as $property) {
            $clone->censored[$property] = true;
        }

        return $clone;
    }

    /**
     * Censor all except the specified properties.
     *
     * @param string[] $properties
     * @return static
     */
    public function only(...$properties)
    {
        $clone = clone $this;

        $clone->default = true;

        foreach ($properties as $property) {
            $clone->censored[$property] = false;
        }

        return $clone;
    }


    /**
     * Invoke the modifier as callback
     *
     * @param EntityInterface $entity
     * @param array|stdClass  $data
     * @return array|stdClass
     */
    public function __invoke(EntityInterface $entity, $data = null)
    {
        if (is_array($data)) {
            return $this->redactArray($data);
        }

        if ($data instanceof stdClass) {
            return $this->redactObject($data);
        }

        throw new InvalidArgumentException("Payload must be an array or stdClass object");
    }

    /**
     * Redact an array
     *
     * @param array $data
     * @return array
     */
    protected function redactArray(array $data): array
    {
        foreach (array_keys($data) as $property) {
            if ($this->hasCensored($property)) {
                unset($data[$property]);
            }
        }

        return $data;
    }

    /**
     * Redact a stdClass object
     *
     * @param stdClass $data
     * @return stdClass
     */
    protected function redactObject(stdClass $data): stdClass
    {
        foreach (get_object_vars($data) as $property) {
            if ($this->hasCensored($property)) {
                unset($data->$property);
            }
        }

        return $data;
    }
}
