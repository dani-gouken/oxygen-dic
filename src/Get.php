<?php


namespace Oxygen\DI;

use Oxygen\DI\Contracts\ExtractionParameterContract;
use Oxygen\DI\Extraction\ContainerExtractor;
use Oxygen\DI\Extraction\ExtractionParameters\ContainerExtractionParameter;

class Get extends AbstractStorable
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

    public function getExtractorClassName(): string
    {
        return ContainerExtractor::class;
    }

    public function getExtractionParameter(): ExtractionParameterContract
    {
        return $this->parameter;
    }

    public function withExtractionParameter(ExtractionParameterContract $parameter): self
    {
        $this->parameter = $parameter;
        return $this;
    }
}
