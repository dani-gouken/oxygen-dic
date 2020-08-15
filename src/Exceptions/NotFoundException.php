<?php
namespace Atom\DI\Exceptions;

use Exception;
use Atom\DI\Contracts\StorageContract;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{
    public function __construct(string $key, ?StorageContract $storage = null)
    {
        if ($storage) {
            $message = "The container is unable to resolve [$key] using the storage [{$storage->getStorageKey()}]";
        } else {
            $message = "The container is unable to resolve [$key].";
        }
        parent::__construct($message);
    }
}
