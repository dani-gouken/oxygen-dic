<?php


namespace Oxygen\DI\Contracts;


use Oxygen\DI\DIC;

interface ExtractorContract
{
    public function extract(ExtractionParameterContract $params, DIC $container);

    public function isValidExtractionParameter(ExtractionParameterContract $params);
}