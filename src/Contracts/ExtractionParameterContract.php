<?php


namespace Atom\DI\Contracts;

interface ExtractionParameterContract
{
    public function getExtractionKey(): string;

    public function getObjectMapping(): MappingContract;

    public function getParameterMapping(): MappingContract;

    public function setParameterMapping(MappingContract $mapping);

    public function setObjectMapping(MappingContract $mapping);
}
