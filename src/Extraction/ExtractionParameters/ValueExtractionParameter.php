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
        return "closure_".rand();
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setValue($value)
    {
        return $this->value = $value;
    }
}
