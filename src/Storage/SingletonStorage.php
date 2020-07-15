<?php


namespace Oxygen\DI\Storage;

use Oxygen\DI\DIC;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;

class SingletonStorage extends AbstractStorage
{
    /**
     * @var DIC
     */
    protected $container;

    /**
     * Resolved values
     * @var array
     */
    public $resolvedValues = [];
    public const STORAGE_KEY = "SINGLETONS";

    public function __construct(DIC $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $key
     * @return mixed
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function get(string $key)
    {
        if (!$this->has($key)) {
            throw new NotFoundException($key, $this);
        }
        if (array_key_exists($key, $this->resolvedValues)) {
            return $this->resolvedValues[$key];
        }
        $value = $this->container->extract($this->getDescriptions()[$key]);
        $this->resolvedValues[$key] = $value;
        return $value;
    }

    /**
     * @return string
     */
    public function getStorageKey(): string
    {
        return self::STORAGE_KEY;
    }
}
