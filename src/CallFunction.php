<?php


namespace Oxygen\DI;

use Oxygen\DI\Contracts\ExtractionParameterContract;
use Oxygen\DI\Extraction\ExtractionParameters\FunctionExtractionParameter;
use Oxygen\DI\Extraction\FunctionExtractor;

class CallFunction extends AbstractStorable
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

    public function getExtractionParameter(): ExtractionParameterContract
    {
        return $this->parameter;
    }

    public function withExtractionParameter(ExtractionParameterContract $extractionParameter): self
    {
        $this->parameter = $extractionParameter;
        return $this;
    }
}
