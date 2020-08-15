<?php


namespace Atom\DI\Storage;

use Atom\DI\Definitions\BuildObject;
use Atom\DI\Definitions\Value;
use Atom\DI\DIC;
use Atom\DI\Exceptions\ContainerException;
use Atom\DI\Exceptions\NotFoundException;

class SingletonStorage extends AbstractStorage
{
    use ClassBindingTrait;
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
        parent::__construct($container);
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
        if (array_key_exists($key, $this->resolvedValues)) {
            return $this->resolvedValues[$key];
        }
        if (!$this->has($key)) {
            throw new NotFoundException($key, $this);
        }
        $value = $this->container->extract($this->getDescriptions()[$key], $key);
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
