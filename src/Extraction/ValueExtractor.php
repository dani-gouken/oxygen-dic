<?php


namespace Oxygen\DI\Extraction;


use Oxygen\DI\Contracts\ExtractionParameterContract;
use Oxygen\DI\Contracts\ExtractorContract;
use Oxygen\DI\DIC;
use Oxygen\DI\Extraction\ExtractionParameters\ValueExtractionParameter;

class ValueExtractor implements ExtractorContract
{

    public function extract(ExtractionParameterContract $params, DIC $container)
    {
        /**
         * @var $params ValueExtractionParameter
         */
        return $params->getValue();
    }

    public function isValidExtractionParameter(ExtractionParameterContract $params)
    {
        return true;
    }
}