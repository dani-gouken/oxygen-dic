<?php


namespace Oxygen\DI\Contracts;

use Oxygen\DI\DIC;
use Oxygen\DI\Exceptions\ContainerException;

interface StorageContract
{
    /**
     * StorageContract constructor.
     * @param DIC $container
     */
    public function __construct(DIC $container);

    /**
     * check ig a value exists on the storage
     * @param string $key
     * @return bool
     */
    public function has(string $key):bool;

    /**
     * check ig a value exists on the storage
     * @param string $key
     * @return bool
     */
    public function get(string $key);

    /**
     * remove a value from the storage. it does not throw if the value doesn't exists
     * @param string $key
     * @return bool
     */
    public function remove(string $key);


    /**
     * The unique identifier of the storage
     * @return string
     */
    public function getStorageKey():string;


    /**
     * add a value to the storage
     * @param string $key
     * @param StorableContract $storable
     * @return mixed
     */
    public function store(string $key, StorableContract $storable);

    /**
     * resolve a value in the storage
     * @param string $key
     * @return mixed
     */
    public function resolve(string $key):StorableContract;

    /**
     * alter a value already stored on the storage
     * @param string $key
     * @throws ContainerException
     * @param callable $extendFunction
     * @return mixed
     */
    public function extends(string $key, callable $extendFunction);
}
