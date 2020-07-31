<?php


namespace Oxygen\DI\Extraction\ExtractionParameters;

use Oxygen\DI\Contracts\ExtractionParameterContract;
use Oxygen\DI\Contracts\MappingContract;
use Oxygen\DI\Mapping\Mapping;

abstract class AbstractExtractionParameter implements ExtractionParameterContract
{
    protected $objectMapping;
    protected $parameterMapping;

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
}
