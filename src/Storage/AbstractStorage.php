<?php


namespace Oxygen\DI\Storage;

use Oxygen\DI\Contracts\StorableContract;
use Oxygen\DI\Contracts\StorageContract;
use Oxygen\DI\DIC;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Extraction\FunctionExtractor;
use Oxygen\DI\Extraction\MethodExtractor;
use Oxygen\DI\Extraction\ObjectExtractor;
use Oxygen\DI\Extraction\ValueExtractor;

/**
 * Class AbstractArrayStorage
 * @property DIC $container
 * @package Oxygen\DI\Storage
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
     * @param StorableContract $value
     */
    public function store(string $key, StorableContract $value)
    {
        $this->descriptions[$key] = $value;
    }

    /**
     * @param string $key
     * @param StorableContract $value
     */
    public function toGet(string $key, StorableContract $value)
    {
        $this->store($key, $value);
    }

    /**
     * @param string $key
     * @return mixed
     * @throws NotFoundException
     */
    public function resolve(string $key): StorableContract
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
        return $this->container->extract($this->resolve($key));
    }


    public function extends(string $key, callable $extendFunction)
    {
        $this->store($key, $extendFunction($this->descriptions[$key]));
    }

    public function toArray(): array
    {
        $data = [];
        foreach ($this->descriptions as $key => $description) {
            $data[$key] = $description->toArray();
        }
        return $data;
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
