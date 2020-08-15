<?php


namespace Atom\DI\Definitions;

use Atom\DI\Contracts\ExtractionParameterContract;
use Atom\DI\Extraction\ExtractionParameters\FunctionExtractionParameter;
use Atom\DI\Extraction\FunctionExtractor;

class CallFunction extends AbstractDefinition
{
    /**
     * @var FunctionExtractionParameter
     */
    private $parameter;
    private $callable;

    public function __construct($callable, array $parameters = [])
    {
        $this->parameter = new FunctionExtractionParameter($callable, $parameters);
        $this->callable = $callable;
    }

    /**
     * @return string
     */
    public function getExtractorClassName(): string
    {
        return FunctionExtractor::class;
    }

    /**
     * @return FunctionExtractionParameter
     */
    public function getExtractionParameter(): ExtractionParameterContract
    {
        return $this->parameter;
    }

    public function withExtractionParameter(FunctionExtractionParameter $extractionParameter): self
    {
        $this->parameter = $extractionParameter;
        return $this;
    }
}
