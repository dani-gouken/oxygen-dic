<?php


namespace Atom\DI\Exceptions;

use Exception;
use Atom\DI\Contracts\DefinitionContract;

class UnsupportedInvokerException extends Exception
{
    public function __construct(string $storageKey, string $key, DefinitionContract $definition, array $acceptedInvoker)
    {
        parent::__construct("The storage [$storageKey] is unable to store [$key].
        	The invoker for an item of the storage [$storageKey] should be in "
            . implode(", ", $acceptedInvoker) . ". " . $definition->getExtractorClassName() . " Given");
    }
}
