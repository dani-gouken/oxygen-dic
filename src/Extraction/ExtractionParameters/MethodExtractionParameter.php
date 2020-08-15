<?php


namespace Atom\DI\Extraction\ExtractionParameters;

use InvalidArgumentException;
use Atom\DI\Contracts\ArraySerializable;
use Atom\DI\Contracts\ExtractionParameterContract;
use Atom\DI\Contracts\DefinitionContract;

class MethodExtractionParameter extends AbstractExtractionParameter implements ExtractionParameterContract
{
    /**
     * if the class given in the constructor is a string or not
     * @var bool
     */
    private $classIsString;

    /**
     * the name of the class that has the method to invoke
     * @var string
     */
    private $className;

    /**
     * the class that has the method to invoke
     * @var string | object
     */
    private $class;

    /**
     * the name of the method to invoke
     * @var string
     */
    private $method;

    /**
     * default parameters that will be used to call the method.
     * it should be an associative array where the key represent the name of the parameter in the function and the value
     * represent the value of the parameter
     * @var array
     */
    private $parameters;


    public function __construct($class, string $method, array $parameters = [])
    {
        $this->method = $method;
        $this->parameters = $parameters;
        $classIsString = is_string($class);
        $classIsObject = is_object($class);
        if (!$classIsString && !$classIsObject) {
            throw new InvalidArgumentException("Parameter 1 should be either a string or an object");
        }
        $this->classIsString = $classIsString;
        if (is_object($class) && !($class instanceof DefinitionContract)) {
            $this->className = get_class($class);
        }
        if ($class instanceof DefinitionContract) {
            $this->className = $class->getExtractionParameter()->getExtractionKey();
        }
        if (is_string($class)) {
            $this->className = $class;
        }
        $this->class = $class;
        parent::__construct();
    }

    public function getExtractionKey(): string
    {
        return $this->className . "::" . $this->method;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return object|string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return bool
     */
    public function classIsString(): bool
    {
        return $this->classIsString;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }


    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }
}
