<?php


namespace Atom\DI\Extraction\ExtractionParameters;

use Atom\DI\Contracts\ExtractionParameterContract;

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
        if (is_numeric($this->value) || is_string($this->value) || is_bool($this->value)) {
            return (string)$this->value;
        }
        if (is_object($this->value)) {
            return get_class($this->value);
        }
        return gettype($this->value) . rand();
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
