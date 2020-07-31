<?php


namespace Oxygen\DI;

use Oxygen\DI\Contracts\ExtractionParameterContract;
use Oxygen\DI\Extraction\ExtractionParameters\ObjectExtractionParameter;
use Oxygen\DI\Extraction\ObjectExtractor;

class BuildObject extends AbstractStorable
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var ObjectExtractionParameter
     */
    private $extractionParameter;

    public function __construct(string $className, array $constructorParameter = [])
    {
        $this->className = $className;
        $this->extractionParameter = new ObjectExtractionParameter($className, $constructorParameter);
    }



    /**
     * @return string
     */
    public function getExtractorClassName(): string
    {
        return ObjectExtractor::class;
    }

    /**
     * @return ExtractionParameterContract
     */
    public function getExtractionParameter(): ExtractionParameterContract
    {
        return $this->extractionParameter;
    }

    /**
     * @param array $constructorParameter
     * @return BuildObject
     */
    public function withConstructorParameters(array $constructorParameter): BuildObject
    {
        $this->extractionParameter->setConstructorArgs($constructorParameter);
        return $this;
    }

    public function withExtractionParameter(ExtractionParameterContract $extractionParameter): self
    {
        $this->extractionParameter = $extractionParameter;
        return $this;
    }
}
