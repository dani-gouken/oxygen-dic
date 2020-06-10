<?php


namespace Oxygen\DI\Contracts;


interface ExtractionParameterContract extends ArraySerializable
{
    public function getExtractionKey(): string;

    public function getObjectMapping(): MappingContract;

    public function getParameterMapping(): MappingContract;

    public function setParameterMapping(MappingContract $mapping);

    public function setObjectMapping(MappingContract $mapping);
}