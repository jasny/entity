<?php

declare(strict_types=1);

namespace Jasny\EntityCollection;

/**
 * An entity collection that works as a map, so a key/value pairs.
 * @see https://en.wikipedia.org/wiki/Associative_array
 */
interface EntityMapInterface extends EntityCollectionInterface, \ArrayAccess
{
}
