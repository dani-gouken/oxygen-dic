<?php

namespace Oxygen\DI\Storage;

use Oxygen\DI\Contracts\DefinitionContract;
use Oxygen\DI\Definitions\BuildObject;
use Oxygen\DI\Definitions\Value;
use Oxygen\DI\DIC;
use Oxygen\DI\Exceptions\UnsupportedInvokerException;
use Oxygen\DI\Extraction\ContainerExtractor;
use Oxygen\DI\Extraction\ObjectExtractor;
use Oxygen\DI\Extraction\ValueExtractor;

class ValueStorage extends AbstractStorage
{
    public const STORAGE_KEY = "VALUES";

    protected $supportedExtractors= [ValueExtractor::class,ObjectExtractor::class,ContainerExtractor::class];

    /**
     * @var DIC
     */
    protected $container;

    public function __construct(DIC $container)
    {
        parent::__construct($container);
        $this->container = $container;
    }


    /**
     * @param string $key
     * @param DefinitionContract $value
     * @return mixed|void
     * @throws UnsupportedInvokerException
     */
    public function store(string $key, DefinitionContract $value)
    {
        if (!$this->supportExtractor($value->getExtractorClassName())) {
            throw new UnsupportedInvokerException($this->getStorageKey(), $key, $value, $this->supportedExtractors);
        }
        parent::store($key, $value);
    }

    /**
     * @return string
     */
    public function getStorageKey(): string
    {
        return self::STORAGE_KEY;
    }

    /**
     * @param $class
     * @throws UnsupportedInvokerException
     */
    public function bind($class)
    {
        if (is_string($class)) {
            $this->store($class, new BuildObject($class));
        } else {
            $this->store(get_class($class), new Value($class));
        }
    }

    /**
     * @param string $className
     * @return BuildObject
     * @throws UnsupportedInvokerException
     */
    public function bindClass(string $className): BuildObject
    {
        $this->store($className, $definition =  new BuildObject($className));
        return $definition;
    }

    /**
     * @param object $object
     * @return Value
     * @throws UnsupportedInvokerException
     */
    public function bindInstance(object $object): Value
    {
        $this->store(get_class($object), $definition =  new Value($object));
        return $definition;
    }
}
