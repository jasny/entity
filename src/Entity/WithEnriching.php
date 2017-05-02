<?php

namespace Jasny\Entity;

/**
 * Entity can be enriched with related data.
 */
interface WithEnriching
{
    /**
     * Enrich entity with related data.
     * Returns a clone of $this with the additional data.
     * 
     * @param string[] $properties
     * @return $this
     */
    public function with(...$properties);
}
