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
     * @param string $className
     * @param StorableContract $storable
     * @return AbstractStorable
     */
    public function with(string $className, StorableContract $storable):self
    {
        $this->getExtractionParameter()->getObjectMapping()->add(new MappingItem($className, $storable));
        return $this;
    }

    /**
     * @param string $parameterName
     * @param StorableContract $storable
     * @return AbstractStorable
     */
    public function withParameter(string $parameterName, StorableContract $storable):self
    {
        $this->getExtractionParameter()->getParameterMapping()->add(new MappingItem($parameterName, $storable));
        return $this;
    }

    public function resolved(?callable $callback):self
    {
        $this->resolutionCallback = $callback;
        return $this;
    }

    public function getResolutionCallback(): ?callable
    {
        return $this->resolutionCallback;
    }
}
