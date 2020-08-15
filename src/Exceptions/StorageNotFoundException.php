<?php


namespace Atom\DI\Exceptions;

class StorageNotFoundException extends ContainerException
{
    public function __construct(string $storageKey)
    {
        parent::__construct("The storage [$storageKey] doesnt exists or is not registered in the container");
    }
}
