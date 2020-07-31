<?php


namespace Oxygen\DI\Mapping;

use Oxygen\DI\Contracts\MappingItemContract;
use Oxygen\DI\Contracts\StorableContract;

class MappingItem implements MappingItemContract
{
    /**
     * @var string
     */
    private $key;
    /**
     * @var StorableContract
     */
    private $storable;

    public function __construct(string $key, StorableContract $storable)
    {
        $this->key = $key;
        $this->storable = $storable;
    }

    public function getMappedEntityKey()
    {
        return $this->key;
    }

    public function getStorable():StorableContract
    {
        return $this->storable;
    }
}
