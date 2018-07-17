<?php

//Temporary place functions in this repo. They should be moved to jasny/php-functions

namespace Jasny;

use Jasny\Entity\LazyLoadingInterface;

/**
 * Set properies of object
 *
 * @param  object  $object
 * @param  array   $data
 * @param  boolean $isDynamic
 */
function object_set_properties($object, array $data, bool $isDynamic): void
{
    $class = get_class($object);
    $reflection = new \ReflectionClass($class);

    foreach ($data as $key => $value) {
        $exists = $reflection->hasProperty($key);

        if ($exists) {
            $property = $reflection->getProperty($key);
            $skip = !$property->isPublic() || $property->isStatic();
        } else {
            $skip = $key[0] === '_' || !$isDynamic;
        }

        if (!$skip) {
            $object->$key = $value;
        }
    }
}

/**
 * Get properies of object
 *
 * @param  object $object
 * @return array
 */
function object_get_properties($object): array
{
    return get_object_vars($object);
}
