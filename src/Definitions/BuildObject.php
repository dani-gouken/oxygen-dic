<?php


namespace Atom\DI\Definitions;

use Atom\DI\Contracts\ExtractionParameterContract;
use Atom\DI\Extraction\ExtractionParameters\ObjectExtractionParameter;
use Atom\DI\Extraction\ObjectExtractor;

class BuildObject extends AbstractDefinition
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
     * @return ObjectExtractionParameter
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

    public function withExtractionParameter(ObjectExtractionParameter $extractionParameter): self
    {
        $this->extractionParameter = $extractionParameter;
        return $this;
    }
}
