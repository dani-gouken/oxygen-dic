<?php


namespace Atom\DI\Definitions;

use Atom\DI\Contracts\ExtractionParameterContract;
use Atom\DI\Extraction\ExtractionParameters\WildcardExtractionParameter;
use Atom\DI\Extraction\WildcardExtractor;

class Wildcard extends AbstractDefinition
{
    /**
     * @var string $pattern
     */
    private $pattern;
    /**
     * @var string $replacement
     */
    private $replacement;

     /**
     * @var string $class
     */
    private $class;

    /**
     * @var WildcardExtractionParameter
     */
    private $parameter;

    public function __construct(string $replacement, ?string $pattern = null)
    {
        $this->pattern = $pattern;
        $this->replacement = $replacement;
    }

    public function getExtractorClassName(): string
    {
        return WildcardExtractor::class;
    }

    public function getExtractionParameter(): ExtractionParameterContract
    {
        if ($this->parameter == null) {
            $this->parameter = new WildcardExtractionParameter(
                $this->getClass(),
                $this->getReplacement(),
                $this->getPattern()
            );
        }
        return $this->parameter;
    }

    /**
     * @return bool
     */
    public function hasPattern():bool
    {
        return $this->pattern != null;
    }

    /**
     * @param string $pattern
     */
    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass(string $class): void
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getReplacement(): string
    {
        return $this->replacement;
    }
}
