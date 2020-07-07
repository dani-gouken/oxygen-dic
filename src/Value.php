<?php


namespace Oxygen\DI;


use Oxygen\DI\Contracts\ExtractionParameterContract;
use Oxygen\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Oxygen\DI\Extraction\ValueExtractor;

class Value extends AbstractStorable
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

    public function getExtractionParameter(): ExtractionParameterContract
    {
        return $this->parameter;
    }

    public static function fromArray($array)
    {
        return self::hydrateExtractionParameterFromArray(new self(
            $array["value"]
        ), $array);
    }

    public function toArray(): array
    {
        return array_merge(["value" => $this->value], $this->extractionParameterToArray());
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
