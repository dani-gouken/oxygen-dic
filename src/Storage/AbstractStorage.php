<?php


namespace Atom\DI\Storage;

use Atom\DI\Contracts\DefinitionContract;
use Atom\DI\Contracts\StorageContract;
use Atom\DI\DIC;
use Atom\DI\Exceptions\ContainerException;
use Atom\DI\Exceptions\NotFoundException;
use Atom\DI\Extraction\FunctionExtractor;
use Atom\DI\Extraction\MethodExtractor;
use Atom\DI\Extraction\ObjectExtractor;
use Atom\DI\Extraction\ValueExtractor;

/**
 * Class AbstractArrayStorage
 * @property DIC $container
 * @package Atom\DI\Storage
 */
abstract class AbstractStorage implements StorageContract
{
    protected $supportedExtractors = [
        ValueExtractor::class,
        ObjectExtractor::class,
        MethodExtractor::class,
        FunctionExtractor::class
    ];
    protected $container;
    /**
     * @var array<DefinitionContract>
     */
    protected $descriptions = [];
    public function __construct(DIC $dic)
    {
        $this->container = $dic;
    }

    public function getContainer(): DIC
    {
        return $this->container;
    }

    /**
     * @param string $extractorClassName
     * @throws ContainerException
     */
    public function addSupportForExtractor(string $extractorClassName): void
    {
        if (!$this->container->hasExtractor($extractorClassName)) {
            throw new ContainerException("You are trying add the support for the invoker [$extractorClassName] 
            in the storage [" . self::class . "], but that invoker is not registered in the container");
        }
        if (!array_key_exists($extractorClassName, $this->supportedExtractors)) {
            $this->supportedExtractors[] = $extractorClassName;
        }
    }

    public function supportExtractor(string $extractorClassName): bool
    {
        return in_array($extractorClassName, $this->supportedExtractors);
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->descriptions);
    }

    public function contains(String $key): bool
    {
        return $this->has($key);
    }

    /**
     * @param string $key
     * @param DefinitionContract $value
     */
    public function store(string $key, DefinitionContract $value)
    {
        $this->descriptions[$key] = $value;
    }


    /**
     * @param string $key
     * @return mixed
     * @throws NotFoundException
     */
    public function resolve(string $key): DefinitionContract
    {
        if (!$this->has($key)) {
            throw new NotFoundException($key, $this);
        }
        return $this->getDescriptions()[$key];
    }

    /**
     * @param string $key
     * @return mixed
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function get(string $key)
    {
        return $this->container->extract($this->resolve($key), $key);
    }


    public function extends(string $key, callable $extendFunction)
    {
        $this->store($key, $extendFunction($this->descriptions[$key]));
    }

    public function getDescriptions()
    {
        return $this->descriptions;
    }

    public function remove(string $key)
    {
        if ($this->has($key)) {
            unset($this->descriptions[$key]);
        }
    }
}
