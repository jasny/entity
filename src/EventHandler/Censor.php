<?php

declare(strict_types=1);

namespace Jasny\Entity\EventHandler;

use Jasny\Entity\Entity;
use function Jasny\expect_type;
use function Jasny\array_without;

/**
 * Censor an entity, removing values for given properties.
 * @immutable
 */
class Censor implements EventHandlerInterface
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
     * @param Entity $entity
     * @param array|\stdClass $data
     * @return array|\stdClass
     */
    public function __invoke(Entity $entity, $data = null)
    {
        expect_type($data, ['array', \stdClass::class]);

        $result = array_without((array)$data, $this->properties);

        return is_object($data) ? (object)$result : $result;
    }
}
