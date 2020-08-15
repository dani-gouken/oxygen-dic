<?php


namespace Atom\DI\Extraction\ExtractionParameters;

class ContainerExtractionParameter extends AbstractExtractionParameter
{
    /**
     * @var string
     */
    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
        parent::__construct();
    }

    public function getExtractionKey(): string
    {
        return $this->key;
    }
}
