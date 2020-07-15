<?php


namespace Oxygen\DI\Extraction\ExtractionParameters;

use Oxygen\DI\Contracts\ArraySerializable;
use Oxygen\DI\Contracts\ExtractionParameterContract;

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

    public static function fromArray($array)
    {
        return self::hydrateMappingFromArray(new self(
            $array["class"],
            $array["constructorArgs"] ?? [],
            $array["cacheResult"]  ?? false
        ), $array);
    }

    public function toArray(): array
    {
        return array_merge([
            "class" => $this->getClassName(),
            "constructorArgs" => $this->getConstructorArgs(),
            'cacheResult' => $this->cacheResult,
        ], $this->mappingToArray());
    }

    protected function constructorArgsToArray()
    {
        return array_map(function ($value) {
            if ($value instanceof ArraySerializable) {
                return [get_class($value) => $value->toArray()];
            }
            return $value;
        }, $this->constructorArgs);
    }

    protected static function hydrateConstructorArgs(ObjectExtractionParameter $parameter, $array)
    {
        return $parameter->constructorArgs = array_map(function ($value) {
            if (is_array($value)) {
                return call_user_func(array(array_key_first($value), end($value)));
            }
            return $value;
        }, $array);
    }
}
