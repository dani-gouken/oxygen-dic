<?php

namespace Oxygen\DI;

use ArrayAccess;
use Oxygen\DI\Contracts\ExtractorContract;
use Oxygen\DI\Contracts\StorableContract;
use Oxygen\DI\Contracts\StorageContract;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\CircularDependencyException;
use Oxygen\DI\Exceptions\StorageNotFoundException;
use Oxygen\DI\Extraction\ContainerExtractor;
use Oxygen\DI\Extraction\ExtractionChain;
use Oxygen\DI\Extraction\FunctionExtractor;
use Oxygen\DI\Extraction\MethodExtractor;
use Oxygen\DI\Extraction\ObjectExtractor;
use Oxygen\DI\Extraction\ValueExtractor;
use Oxygen\DI\Storage\FactoryStorage;
use Oxygen\DI\Storage\SingletonStorage;
use Oxygen\DI\Storage\ValueStorage;
use Psr\Container\ContainerInterface;

class DIC implements ContainerInterface, ArrayAccess
{

    /**
     * @var array<ExtractorContract>
     */
    private $extractors = [];
    /**
     * @var StorageContract[]
     */
    private $container = [];

    /**
     * @var array
     */
    public $resolvedValues = [];
    /**
     * @var $instance DIC
     */
    private static $instance;

    /**
     * @var string
     */
    private $defaultStorageAlias = SingletonStorage::STORAGE_KEY;
    /**
     * @var ExtractionChain
     */
    private $chain;

    /**
     * @return DIC
     * @throws ContainerException
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * DIC constructor.
     * @throws ContainerException
     */
    public function __construct()
    {
        $this->addStorage(new FactoryStorage($this));
        $this->addStorage(new SingletonStorage($this));
        $this->addStorage(new ValueStorage($this));
        $this->extractors = [
            MethodExtractor::class => new MethodExtractor(),
            ObjectExtractor::class => new ObjectExtractor(),
            FunctionExtractor::class => new FunctionExtractor(),
            ValueExtractor::class => new ValueExtractor(),
            ContainerExtractor::class => new ContainerExtractor()
        ];
        $this->chain = new ExtractionChain();
        self::$instance = $this;
    }

    /**
     * @param string $extractorClassName
     * @return ExtractorContract
     * @throws ContainerException
     */
    public function getExtractor(string $extractorClassName): ExtractorContract
    {
        if (!$this->hasExtractor($extractorClassName)) {
            throw new ContainerException("Unable to resolve the extractor [$extractorClassName]");
        }
        return $this->extractors[$extractorClassName];
    }

    public function hasExtractor($extractorClassName)
    {
        return array_key_exists($extractorClassName, $this->extractors);
    }

    public function addExtractor(ExtractorContract $extractor): void
    {
        $this->extractors[get_class($extractor)] = $extractor;
    }

    /**
     * Add a new storage. will throw if a storage with a similar key already exists
     * @param StorageContract $storage
     * @throws ContainerException
     */
    public function addStorage(StorageContract $storage)
    {
        if (array_key_exists($storageKey = $storage->getStorageKey(), $this->getContainer())) {
            throw new ContainerException("A storage with the key [$storageKey] already exists");
        }
        $this->container[$storage->getStorageKey()] = $storage;
    }


    /**
     * check if the storage exists
     * @param string $key
     * @return bool
     */
    public function hasStorage(string $key): bool
    {
        return array_key_exists($key, $this->container);
    }

    /**
     * get a storage
     * @param string $key
     * @return StorageContract
     * @throws StorageNotFoundException
     */
    public function getStorage(string $key): StorageContract
    {
        if (!$this->hasStorage($key)) {
            throw new StorageNotFoundException($key);
        }
        return $this->container[$key];
    }

    /**
     * return the container
     * @return StorageContract[]
     */
    public function getContainer(): array
    {
        return $this->container;
    }

    /**
     * return the default storage
     * @return StorageContract
     * @throws StorageNotFoundException
     */
    public function getDefaultStorage(): StorageContract
    {
        return $this->getStorage($this->defaultStorageAlias);
    }

    /**
     * return the default storage alias
     * @return string
     */
    public function getDefaultStorageAlias(): String
    {
        return $this->defaultStorageAlias;
    }

    /**
     * return the factory storage
     * @return FactoryStorage
     * @throws StorageNotFoundException
     */
    public function factory(): FactoryStorage
    {
        /**
         * @var FactoryStorage $result
         */
        $result = $this->getStorage(FactoryStorage::STORAGE_KEY);
        return $result;
    }

    /**
     * return the singleton storage
     * @return SingletonStorage
     * @throws StorageNotFoundException
     */
    public function singleton(): SingletonStorage
    {
        /** @var SingletonStorage $result */
        $result = $this->getStorage(SingletonStorage::STORAGE_KEY);
        return $result;
    }

    /**
     * return the value storage
     * @return ValueStorage
     * @throws StorageNotFoundException
     */
    public function value(): ValueStorage
    {
        /**
         * @var ValueStorage $result
         */
        $result = $this->getStorage(ValueStorage::STORAGE_KEY);
        return $result;
    }

    /**
     * @param StorableContract $storable
     * @return mixed
     * @throws ContainerException
     */
    public function extract(StorableContract $storable)
    {
        $extractionParameter = $storable->getExtractionParameter();
        $extractor = $this->getExtractor($extractorClassName = $storable->getExtractorClassName());
        if (!$extractor->isValidExtractionParameter($extractionParameter)) {
            $extractionParameterClassName = get_class($extractionParameter);
            throw new ContainerException("[$extractionParameterClassName] is not a valid parameter 
                for the extractor [$extractorClassName]");
        }
        return $extractor->extract($extractionParameter, $this);
    }

    /**
     * @param StorableContract $storable
     * @param string $dependencyAlias
     * @return mixed
     * @throws CircularDependencyException
     * @throws ContainerException
     */
    public function extractDependency(StorableContract $storable, string $dependencyAlias)
    {
        $this->chain->append($dependencyAlias);
        return $this->extract($storable);
    }


    /**
     * Return a value store inside the container
     * @param string $alias
     * @param string|null $storage
     * @param array $args
     * @param bool $makeIfNotAvailable
     * @return mixed|void
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function get($alias, ?string $storage = null, $args = [], $makeIfNotAvailable = true)
    {
        $this->chain->clear();
        return $this->getDependency($alias, $storage, $args, $makeIfNotAvailable);
    }

    /**
     * Return a value store inside de container
     * @param string $alias
     * @param array $args
     * @param $storage
     * @param bool $makeIfNotAvailable
     * @return mixed|void
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     * @throws CircularDependencyException
     */
    public function getDependency(String $alias, ?string $storage = null, $args = [], bool $makeIfNotAvailable = true)
    {
        $this->chain->append($alias);
        if (!$this->has($alias, $storage) && $makeIfNotAvailable) {
            return  $this->make($alias, is_array($args) ? $args : [$args]);
        }
        if (!is_null($storage)) {
            return $this->getStorage($storage)->get($alias);
        }
        $storage = $this->getStorageFor($alias);
        return $storage->get($alias);
    }

    /**
     * check if the container can build the object that has the given alias
     * @param string $alias
     * @param string $storage
     * @return bool
     * @throws StorageNotFoundException
     */
    public function has($alias, ?string $storage = null)
    {
        if (!is_null($storage)) {
            return $this->getStorage($storage)->has($alias);
        }
        foreach ($this->container as $storage) {
            if ($storage->has($alias)) {
                $this->resolvedValues[$alias] = $storage->getStorageKey();
                return true;
            }
        }
        return false;
    }

    /**
     * @param $alias
     * @return StorageContract
     * @throws NotFoundException
     */
    public function getStorageFor($alias): StorageContract
    {
        if (array_key_exists($alias, $this->resolvedValues)) {
            return $this->container[$this->resolvedValues[$alias]];
        }
        foreach ($this->container as $storage) {
            if ($storage->has($alias)) {
                $this->resolvedValues[$alias] = $storage->getStorageKey();
                return $storage;
            }
        }
        throw new NotFoundException($alias);
    }


    /**
     * parse the the [storage::key] notation
     *
     * @param $offset
     * @return array
     */
    private function parseOffset($offset)
    {
        $result = explode('::', $offset);
        if (count($result) <= 1) {
            return [
                'value' => $offset
            ];
        }
        $storage = $result[0];
        unset($result[0]);
        $value = implode('::', $result);
        if (!$this->hasStorage($storage)) {
            return [
                'value' => $offset
            ];
        }
        return [
            'storage' => $storage,
            'value' => $value
        ];
    }

    /**
     * @param mixed $offset
     * @return bool
     * @throws StorageNotFoundException
     */
    public function offsetExists($offset)
    {
        $data = $this->parseOffset($offset);
        return $this->has($data['value'], $data['container']);
    }

    /**
     * @param mixed $offset
     * @return mixed|void
     * @throws ContainerException
     * @throws NotFoundException
     * @throws CircularDependencyException
     */
    public function offsetGet($offset)
    {
        $data = $this->parseOffset($offset);
        return $this->get($data['value'], $data['storage'] ?? null, []);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws ContainerException
     * @throws StorageNotFoundException
     */
    public function offsetSet($offset, $value)
    {
        $data = $this->parseOffset($offset);
        if (!array_key_exists("storage", $data)) {
            $this->getDefaultStorage()->store($offset, $value);
            return;
        }
        $storage = $data["storage"];
        $key = $data["value"];
        $this->getStorage($storage)->store($key, $value);
    }

    /**
     * @param mixed $offset
     * @throws StorageNotFoundException
     */
    public function offsetUnset($offset)
    {
        $data = $this->parseOffset($offset);
        if (array_key_exists("storage", $data)) {
            $storage = $data["storage"];
            $key = $data["value"];
            $this->getStorage($storage)->remove($key);
            return;
        }
        foreach ($this->getContainer() as $storageKey => $storage) {
            $storage->remove($storageKey);
        }
        return;
    }

    /**
     * @param string $alias
     * @param array $params
     * @return mixed
     * @throws ContainerException
     */
    public function make(string $alias, array $params = [])
    {
        return $this->extract(new BuildObject($alias, $params));
    }

    public function toArray(): array
    {
        $data = [];
        /**
         * AbstractStorable $storage
         */
        foreach ($this->container as $storage) {
            $data[$storage->getStorageKey()] = $storage->toArray();
        }
        return $data;
    }

    /**
     * Return all the available extractors
     * @return ExtractorContract[]
     */
    public function getExtractors(): array
    {
        return $this->extractors;
    }

    public function getExtractionChain(): ExtractionChain
    {
        return $this->chain;
    }
}
