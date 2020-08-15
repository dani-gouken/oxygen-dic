<?php


namespace Atom\DI\Extraction;

use Atom\DI\Contracts\ExtractionParameterContract;
use Atom\DI\Contracts\ExtractorContract;
use Atom\DI\DIC;
use Atom\DI\Extraction\ExtractionParameters\ValueExtractionParameter;

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
        return $params instanceof  ValueExtractionParameter;
    }
}
