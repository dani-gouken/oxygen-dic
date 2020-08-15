<?php

namespace Atom\DI\Storage;

use Atom\DI\Contracts\DefinitionContract;
use Atom\DI\Definitions\BuildObject;
use Atom\DI\Definitions\Value;
use Atom\DI\DIC;
use Atom\DI\Exceptions\UnsupportedInvokerException;
use Atom\DI\Extraction\ContainerExtractor;
use Atom\DI\Extraction\ObjectExtractor;
use Atom\DI\Extraction\ValueExtractor;

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
