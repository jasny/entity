<?php

namespace Jasny\Entity\Handler;

use Jasny\EntityInterface;
use Jasny\Meta\Factory as MetaFactory;
use Jasny\Meta;
use ReflectionClass;

/**
 * Filter properties base on meta data
 */
class MetaFilter
{
    /**
     * @var string
     */
    protected $tag;

    /**
     * @var MetaFactory
     */
    protected $metaFactory;

    /**
     * MetaFilter constructor.
     *
     * @param string      $tag          Tag to filter on
     * @param MetaFactory $metaFactory
     */
    public function __construct(string $tag, MetaFactory $metaFactory)
    {
        $this->tag = $tag;
        $this->metaFactory = $metaFactory;
    }

    /**
     * Get the tag to filter on.
     *
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }


    /**
     * Invoke the modifier as callback
     *
     * @param EntityInterface $entity
     * @param array|stdClass  $data
     * @return array|stdClass
     * @throws \ReflectionException
     */
    public function __invoke(EntityInterface $entity, $data = null)
    {
        $reflection = new ReflectionClass(get_class($entity));
        $meta = $this->metaFactory->create($reflection); // Factory will cache

        if (is_array($data)) {
            return $this->redactArray($data, $meta);
        }

        if ($data instanceof stdClass) {
            return $this->redactObject($data, $meta);
        }

        throw new InvalidArgumentException("Payload must be an array or stdClass object");
    }

    /**
     * Redact an array
     *
     * @param array $data
     * @param Meta  $meta
     * @return array
     */
    protected function redactArray(array $data, Meta $meta): array
    {
        foreach (array_keys($data) as $property) {
            if ($meta->ofProperty($property)[$this->tag]) {
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
    protected function redactObject(stdClass $data, Meta $meta): stdClass
    {
        foreach (get_object_vars($data) as $property) {
            if ($meta->ofProperty($property)[$this->tag]) {
                unset($data->$property);
            }
        }

        return $data;
    }
}
