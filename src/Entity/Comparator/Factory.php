<?php

namespace Jasny\Entity\Comparator;

use SebastianBergmann\Comparator\Factory as Base;

/**
 * Factory for comparators which compare values for equality.
 */
class Factory extends Base
{
    /**
     * Constructs a new factory.
     */
    public function __construct()
    {
        parent::__construct();
        
        //$this->register(new EntityComparator);
        //$this->register(new EntityCollectionComparator);
    }
}
