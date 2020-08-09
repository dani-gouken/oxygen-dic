<?php


namespace Oxygen\DI;

use Oxygen\DI\Contracts\StorableContract;
use Oxygen\DI\Mapping\MappingItem;

abstract class AbstractStorable implements StorableContract
{
    /**
     * @var callable
     */
    protected $resolutionCallback;

    /**
     * @param string $mappedObjectClassName
     * @param StorableContract $storable
     * @return AbstractStorable
     */
    public function bind(string $mappedObjectClassName, StorableContract $storable):self
    {
        $this->getExtractionParameter()->getObjectMapping()->add(new MappingItem($mappedObjectClassName, $storable));
        return $this;
    }

    /**
     * @param string $mappedParameterName
     * @param StorableContract $storable
     * @return AbstractStorable
     */
    public function withParameter(string $mappedParameterName, StorableContract $storable):self
    {
        $this->getExtractionParameter()->getParameterMapping()->add(new MappingItem($mappedParameterName, $storable));
        return $this;
    }

    public function resolved(?callable $callback)
    {
        return $this->resolutionCallback = $callback;
    }

    public function getResolutionCallback(): ?callable
    {
        return $this->resolutionCallback;
    }
}
