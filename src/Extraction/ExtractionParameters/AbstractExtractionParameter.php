<?php


namespace Oxygen\DI\Extraction\ExtractionParameters;


use Oxygen\DI\Contracts\ExtractionParameterContract;
use Oxygen\DI\Contracts\MappingContract;
use Oxygen\DI\Mapping\Mapping;

abstract class AbstractExtractionParameter implements ExtractionParameterContract
{
    protected $objectMapping;
    protected $parameterMapping;
    protected $resolutionChain = [];

    public function appendToResolutionChain(string $alias)
    {
        $this->resolutionChain[] = $alias;
    }

    public function getResolutionChain(): array
    {
        return $this->resolutionChain;
    }

    public function __construct()
    {
        $this->objectMapping = new Mapping();
        $this->parameterMapping = new Mapping();
    }

    public function getObjectMapping(): MappingContract
    {
        return $this->objectMapping;
    }

    public function getParameterMapping(): MappingContract
    {
        return $this->parameterMapping;
    }

    public function setParameterMapping(MappingContract $mapping): self
    {
        $this->parameterMapping = $mapping;
        return $this;
    }

    public function setObjectMapping(MappingContract $mapping): self
    {
        $this->objectMapping = $mapping;
        return $this;
    }

    protected function mappingToArray()
    {
        return [
            "mapping" => [
                "parameter_mapping" => $this->getParameterMapping()->toArray(),
                "object_mapping" => $this->getObjectMapping()->toArray()
            ]
        ];
    }

    public static function hydrateMappingFromArray(ExtractionParameterContract $parameter, $mappingArray)
    {
        $parameter->setObjectMapping(Mapping::fromArray($mappingArray["mapping"]["object_mapping"]));
        $parameter->setParameterMapping(Mapping::fromArray($mappingArray["mapping"]["parameter_mapping"]));
        return $parameter;
    }
}