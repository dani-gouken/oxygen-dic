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
    use ClassBindingTrait;
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
}
