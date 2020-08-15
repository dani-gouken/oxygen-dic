<?php


namespace Atom\DI\Extraction\ExtractionParameters;

use Atom\DI\Contracts\ExtractionParameterContract;
use Atom\DI\Contracts\MappingContract;
use Atom\DI\Mapping\Mapping;

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
