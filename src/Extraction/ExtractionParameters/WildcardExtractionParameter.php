<?php


namespace Atom\DI\Extraction\ExtractionParameters;

class WildcardExtractionParameter extends AbstractExtractionParameter
{
    /**
     * @var string
     */
    private $replacement;

     /**
     * @var string
     */
    private $pattern;

    /**
     * @var String
     */
    private $className;
    public function __construct(String $className, string $replacement, string $pattern)
    {
        $this->replacement = $replacement;
        $this->className = $className;
        $this->pattern = $pattern;
        parent::__construct();
    }
    public function getExtractionKey(): string
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getReplacement(): string
    {
        return $this->replacement;
    }

    /**
     * @return String
     */
    public function getClassName(): String
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }
}
