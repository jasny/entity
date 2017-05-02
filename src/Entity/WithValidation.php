<?php

namespace Jasny\Entity;

/**
 * Entity supports validation
 */
interface WithValidation
{
    /**
     * Validate the entity
     * 
     * @return Jasny\ValidationResult
     */
    public function validate();
}
