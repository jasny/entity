<?php

declare(strict_types=1);

namespace Jasny\Entity\Handler;

use Jasny\EntityInterface;
use Jasny\Meta\Factory as MetaFactory;
use Jasny\Meta;
use ReflectionClass;

/**
 * Filter properties base on meta data
 */
class MetaFilter implements HandlerInterface
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
     */
    public function __invoke(EntityInterface $entity, $data = null)
    {
        $meta = $this->metaFactory->create(get_class($entity)); // Factory will cache
        $result = $this->redactArray((array)$data, $meta);

        return is_object($data) ? (object)$result : $result;
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
            if ($meta->ofProperty($property)->get($this->tag, false)) {
                unset($data[$property]);
            }
        }

        return $data;
    }
}
