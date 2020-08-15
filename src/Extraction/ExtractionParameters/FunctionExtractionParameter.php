<?php


namespace Atom\DI\Extraction\ExtractionParameters;

use InvalidArgumentException;
use Atom\DI\Contracts\ExtractionParameterContract;

class FunctionExtractionParameter extends AbstractExtractionParameter implements ExtractionParameterContract
{
    private $method;
    private $methodIsString;
    /**
     * @var array
     */
    private $parameters;


    public function __construct($method, array $parameters = [])
    {
        $methodIsCallable = is_callable($method);
        $methodIsString = is_string($method);
        if (!$methodIsString && !$methodIsCallable) {
            throw new InvalidArgumentException("Parameter 1 should be either a string or a callable");
        }
        $this->method = $method;
        $this->parameters = $parameters;
        $this->methodIsString = $methodIsString;
        parent::__construct();
    }

    public function getExtractionKey(): string
    {
        return $this->methodIsString ? $this->method : "closure_".rand();
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }
    public function getParameters()
    {
        return $this->parameters;
    }
    /**
     * @return bool
     */
    public function methodIsString(): bool
    {
        return $this->methodIsString;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }
}
