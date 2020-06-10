<?php


namespace Oxygen\DI\Storage;

use Oxygen\DI\Contracts\StorableContract;
use Oxygen\DI\DIC;
use Oxygen\DI\Exceptions\UnsupportedInvokerException;
use Oxygen\DI\Extraction\FunctionExtractor;
use Oxygen\DI\Extraction\MethodExtractor;

class FactoryStorage extends  AbstractStorage
{
    protected $supportedExtractors= [FunctionExtractor::class,MethodExtractor::class];
    /**
     * @var DIC
     */
    protected $container;
    public const STORAGE_KEY = "FACTORIES";

    public function __construct(DIC $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $key
     * @param $value
     * @return mixed|void
     * @throws UnsupportedInvokerException
     */
    public function store(string $key, StorableContract $value)
    {
        if(!$this->supportExtractor($value->getExtractorClassName())){
            throw new UnsupportedInvokerException($this->getStorageKey(),$key,$value,$this->supportedExtractors);
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