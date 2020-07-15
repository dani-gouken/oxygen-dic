<?php


namespace Oxygen\DI\Extraction\ExtractionParameters;

use Oxygen\DI\Contracts\ExtractionParameterContract;

class ValueExtractionParameter extends AbstractExtractionParameter implements ExtractionParameterContract
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
        parent::__construct();
    }

    public function getExtractionKey(): string
    {
        if (is_string($this->value) || is_numeric($this->value)) {
            return (string) $this->value;
        }
        if (is_object($this->value)) {
            return get_class($this->value);
        }
        return "__closure__";
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function setValue($value)
    {
        return $this->value = $value;
    }

    public static function fromArray($array)
    {
        return self::hydrateMappingFromArray(new self(
            $array["value"]
        ), $array);
    }

    public function toArray(): array
    {
        return array_merge([
            "value" => $this->getValue(),
        ], $this->mappingToArray());
    }
}
