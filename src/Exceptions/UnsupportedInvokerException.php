<?php


namespace Oxygen\DI\Exceptions;


use Exception;
use Oxygen\DI\Contracts\StorableContract;

class UnsupportedInvokerException extends Exception
{
    public function __construct(string $storageKey, string $key, StorableContract $storable, array $acceptedInvoker)
    {
        parent::__construct("The storage [$storageKey] is unable to store [$key].The invoker for an item of the storage [$storageKey] should be in "
            . implode(", ", $acceptedInvoker) . ". " . $storable->getExtractorClassName() . " Given");
    }
}
