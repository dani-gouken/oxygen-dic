<?php


namespace Atom\DI\Definitions;

use Atom\DI\Contracts\DefinitionContract;
use Atom\DI\Mapping\MappingItem;

abstract class AbstractDefinition implements DefinitionContract
{
    /**
     * @var callable
     */
    protected $resolutionCallback;

    /**
     * @param string $className
     * @param DefinitionContract $definition
     * @return AbstractDefinition
     */
    public function with(string $className, DefinitionContract $definition):self
    {
        $this->getExtractionParameter()->getObjectMapping()->add(new MappingItem($className, $definition));
        return $this;
    }

    /**
     * @param string $parameterName
     * @param DefinitionContract $definition
     * @return AbstractDefinition
     */
    public function withParameter(string $parameterName, DefinitionContract $definition):self
    {
        $this->getExtractionParameter()->getParameterMapping()->add(new MappingItem($parameterName, $definition));
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
