<?php


namespace Atom\DI\Contracts;

use Atom\DI\DIC;

interface ExtractorContract
{
    public function extract(ExtractionParameterContract $params, DIC $container);

    public function isValidExtractionParameter(ExtractionParameterContract $params);
}
