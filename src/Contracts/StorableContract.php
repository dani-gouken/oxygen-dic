<?php


namespace Oxygen\DI\Contracts;

interface StorableContract
{
    public function getExtractorClassName(): string;

    public function getExtractionParameter(): ExtractionParameterContract;
}
