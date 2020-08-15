<?php


namespace Atom\DI\Definitions;

use Nette\PhpGenerator\Method;
use Atom\DI\Contracts\ExtractionParameterContract;
use Atom\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Atom\DI\Extraction\ValueExtractor;

class Value extends AbstractDefinition
{
    private $value;
    /**
     * @var ValueExtractionParameter
     */
    private $parameter;

    public function __construct($value)
    {
        $this->value = $value;
        $this->parameter = new ValueExtractionParameter($this->value);
    }

    public function getExtractorClassName(): string
    {
        return ValueExtractor::class;
    }

    /**
     * @return ValueExtractionParameter
     */
    public function getExtractionParameter(): ExtractionParameterContract
    {
        return $this->parameter;
    }

    public function withExtractionParameter(ExtractionParameterContract $parameter): self
    {
        $this->parameter = $parameter;
        return $this;
    }

    public function setValue($value)
    {
        $this->parameter->setValue($value);
    }

    public function getValue()
    {
        return $this->parameter->getValue();
    }
}
