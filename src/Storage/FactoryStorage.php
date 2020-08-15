<?php


namespace Atom\DI\Storage;

use Atom\DI\Contracts\DefinitionContract;
use Atom\DI\DIC;
use Atom\DI\Exceptions\UnsupportedInvokerException;
use Atom\DI\Extraction\FunctionExtractor;
use Atom\DI\Extraction\MethodExtractor;

class FactoryStorage extends AbstractStorage
{
    protected $supportedExtractors= [FunctionExtractor::class,MethodExtractor::class];
    /**
     * @var DIC
     */
    protected $container;
    public const STORAGE_KEY = "FACTORIES";

    public function __construct(DIC $container)
    {
        parent::__construct($container);
        $this->container = $container;
    }

    /**
     * @param string $key
     * @param $value
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
     * The unique identifier of the storage
     * @return string
     */
    public function getStorageKey(): string
    {
        return self::STORAGE_KEY;
    }
}
