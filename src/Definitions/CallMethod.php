<?php


namespace Atom\DI\Definitions;

use InvalidArgumentException;
use Atom\DI\Contracts\ExtractionParameterContract;
use Atom\DI\Extraction\ExtractionParameters\FunctionExtractionParameter;
use Atom\DI\Extraction\ExtractionParameters\MethodExtractionParameter;
use Atom\DI\Extraction\MethodExtractor;

class CallMethod extends AbstractDefinition
{
    /**
     * @var object
     */
    private $object;
    /**
     * @var string
     */
    private $methodName;
    /**
     * @var array
     */
    private $parameter;

    /**
     * @var MethodExtractionParameter
     */
    private $extractionParameter;

    public function __construct(string $methodName = "__invoke", array $parameters = [])
    {
        $this->methodName = $methodName;
        $this->parameter = $parameters;
    }

    /**
     * @return MethodExtractionParameter
     */
    public function getExtractionParameter(): ExtractionParameterContract
    {
        if ($this->extractionParameter != null) {
            return $this->extractionParameter;
        }
        if ($this->object == null) {
            throw new InvalidArgumentException("You need to specify the object on which the method should be called");
        }
        $this->extractionParameter = new MethodExtractionParameter($this->object, $this->methodName, $this->parameter);
        return $this->extractionParameter;
    }

    /**
     * @return string
     */
    public function getExtractorClassName(): string
    {
        return MethodExtractor::class;
    }

    /**
     * @param $object
     * @return CallMethod
     */
    public function on($object):self
    {
        $this->object = $object;
        return $this;
    }

    public function withExtractionParameter(MethodExtractionParameter $extractionParameter): self
    {
        $this->extractionParameter = $extractionParameter;
        return $this;
    }
}
