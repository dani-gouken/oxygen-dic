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

    public static function fromArray($array)
    {
        $key = array_key_first($array);
        $storable = $array[$key];
        $storableKey = array_key_first($storable);
        return new MappingItem($key,call_user_func(array($storableKey, 'fromArray'),$storable[$storableKey]));
    }

    public function toArray(): array
    {
        return [$this->getMappedEntityKey() => [get_class($this->storable) => $this->storable->toArray()]];
    }
}