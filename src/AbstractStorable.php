<?php


namespace Oxygen\DI;

use Oxygen\DI\Contracts\StorableContract;
use Oxygen\DI\Mapping\MappingItem;

abstract class AbstractStorable implements StorableContract
{

    /**
     * @param string $mappedObjectClassName
     * @param StorableContract $storable
     * @return AbstractStorable
     */
    public function give(string $mappedObjectClassName, StorableContract $storable):self
    {
        $this->getExtractionParameter()->getObjectMapping()->add(new MappingItem($mappedObjectClassName, $storable));
        return $this;
    }

    /**
     * @param string $mappedParameterName
     * @param StorableContract $storable
     * @return AbstractStorable
     */
    public function giveParameter(string $mappedParameterName, StorableContract $storable):self
    {
        $this->getExtractionParameter()->getParameterMapping()->add(new MappingItem($mappedParameterName, $storable));
        return $this;
    }
}
