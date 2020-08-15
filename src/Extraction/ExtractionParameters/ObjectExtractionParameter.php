<?php


namespace Atom\DI\Extraction\ExtractionParameters;

use Atom\DI\Contracts\ArraySerializable;
use Atom\DI\Contracts\ExtractionParameterContract;

class ObjectExtractionParameter extends AbstractExtractionParameter implements ExtractionParameterContract
{
    /**
     * @var string
     */
    private $className;
    /**
     * @var array
     */
    private $constructorArgs;
    /**
     * @var bool
     */
    private $cacheResult;

    public function __construct(string $className, array $constructorArgs = [], $cacheResult = false)
    {
        $this->className = $className;
        $this->constructorArgs = $constructorArgs;
        $this->cacheResult = $cacheResult;
        parent::__construct();
    }

    public function getExtractionKey(): string
    {
        return $this->className;
    }

    /**
     * @return array
     */
    public function getConstructorArgs(): array
    {
        return $this->constructorArgs;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return bool
     */
    public function canCacheResult(): bool
    {
        return $this->cacheResult;
    }

    /**
     * @param array $constructorArgs
     */
    public function setConstructorArgs(array $constructorArgs): void
    {
        $this->constructorArgs = $constructorArgs;
    }
}
