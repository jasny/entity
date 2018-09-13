<?php

declare(strict_types=1);

namespace Jasny\Entity\Handler;

use Jasny\Entity\EntityInterface;
use function Jasny\expect_type;
use function Jasny\array_only;

/**
 * Redact an entity, removing values except for given properties.
 * @immutable
 */
class Redact
{
    /**
     * @var array
     */
    protected $properties;


    /**
     * Redact properties.
     *
     * @param string[] $properties
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }


    /**
     * Invoke the modifier as callback
     *
     * @param EntityInterface $entity
     * @param array|\stdClass $data
     * @return array|\stdClass
     */
    public function __invoke(EntityInterface $entity, $data = null)
    {
        expect_type($data, ['array', \stdClass::class]);

        $result = array_only((array)$data, $this->properties);

        return is_object($data) ? (object)$result : $result;
    }
}
