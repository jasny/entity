<?php

namespace Jasny\Entity;

use Jasny\Entity\EnrichingInterface;
use Jasny\Meta\Introspection;

/**
 * Entity can be enriched with related data
 */
trait CensoringTrait
{
    /**
     * @var bool
     * @ignore
     */
    private $i__censor_default = false;
    
    /**
     * @var array
     * @ignore
     */
    private $i__censored = [];
    
    
    /**
     * Set if properties are censored by default
     * 
     * @param bool $censored
     * @return void
     */
    final protected function censorByDefault(bool $censored)
    {
        $this->i__censor_default = $censored;
    }
    
    /**
     * Get if properties are censored by default
     * 
     * @return bool
     */
    final protected function isCensoredByDefault(): bool
    {
        return $this->i__censor_default;
    }
    
    /**
     * Mark a property to be censored (or not)
     * 
     * @param string $property
     * @param bool   $censored
     */
    final protected function markAsCensored(string $property, bool $censored)
    {
        $this->i__censored[$property] = $censored;
    }

    /**
     * Check if a property is marked as censored
     * 
     * @param string $property
     * @return bool|null
     */
    final protected function hasMarkedAsCensored(string $property)
    {
        return isset($this->i__censored[$property]) ? $this->i__censored[$property] : null;
    }
    
    /**
     * Remove all properties as censored list
     * 
     * @return void
     */
    final protected function resetMarkedAsCensored()
    {
        $this->i__censor_default = false;
        $this->i__censored = [];
    }
    
    /**
     * Get all properties that are marked as censored
     * 
     * @return string[]
     */
    final protected function getAllMarkedAsCensored(): array
    {
       return $this->i__censored; 
    }
    
    
    /**
     * Check if the property is censored for this Entity
     * 
     * @param string $property
     * @return bool
     */
    public function hasCensored(string $property): bool
    {
        $censored = $this->hasMarkedAsCensored($property);
        
        if (!isset($censored) && $this instanceof Introspection) {
            $censored = static::meta()->ofProperty($property)['censor'];
        }
        
        if (!isset($censored)) {
            $censored = $this->isCensoredByDefault();
        }
        
        return $censored;
    }
    

    /**
     * Censor properties from entity.
     * 
     * @param string[] $properties
     * @return static
     */
    public function without(string ...$properties): self
    {
        foreach ($properties as $property) {
            $this->markAsCensored($property, true);
        }
        
        return $this;
    }
    
    /**
     * Censor all  only the specified properties.
     * Enriches with related data if needed.
     * 
     * @param string[] $properties
     * @return static
     */
    public function withOnly(string ...$properties): self
    {
        $this->censorByDefault(true);

        if ($this instanceof EnrichingInterface) {
            $this->with(...$properties);
        } else {
            foreach ($properties as $property) {
                $this->markAsCensored($property, false);
            }
        }
        
        return $this;
    }
}
