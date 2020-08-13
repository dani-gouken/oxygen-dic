<?php


namespace Oxygen\DI\Definitions;

use Nette\PhpGenerator\Method;
use Oxygen\DI\Contracts\ExtractionParameterContract;
use Oxygen\DI\Extraction\ContainerExtractor;
use Oxygen\DI\Extraction\ExtractionParameters\ContainerExtractionParameter;

class Get extends AbstractDefinition
{
    /**
     * @var string
     */
    private $key;
    /**
     * @var ContainerExtractionParameter
     */
    private $parameter;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->parameter = new ContainerExtractionParameter($key);
    }

    /**
     * @return string
     */
    public function getExtractorClassName(): string
    {
        return ContainerExtractor::class;
    }

    /**
     * @return ContainerExtractionParameter
     */
    public function getExtractionParameter(): ExtractionParameterContract
    {
        return $this->parameter;
    }

    /**
     * @param ContainerExtractionParameter $parameter
     * @return $this
     */
    public function withExtractionParameter(ContainerExtractionParameter $parameter): self
    {
        $this->parameter = $parameter;
        return $this;
    }
}
