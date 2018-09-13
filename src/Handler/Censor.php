<?php

declare(strict_types=1);

namespace Jasny\Entity\Handler;

use Jasny\Entity\EntityInterface;

use function Jasny\expect_type;
use function Jasny\array_without;

/**
 * Censor an entity, removing values for given properties.
 * @immutable
 */
class Censor
{
    /**
     * @var array
     */
    protected $properties;


    /**
     * Censor properties.
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
        expect_type($entity, ['array', \stdClass::class]);

        $result = array_without((array)$data, $this->properties);

        return is_object($data) ? (object)$result : $result;
    }
}
