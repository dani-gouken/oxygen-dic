<?php


namespace Oxygen\DI\Contracts;

interface StorableContract extends ArraySerializable
{
    public function getExtractorClassName(): string;

    public function getExtractionParameter(): ExtractionParameterContract;

    public function withExtractionParameter(ExtractionParameterContract $extractionParameter);
}
