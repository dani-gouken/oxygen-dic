<?php


namespace Atom\DI\Mapping;

use Atom\DI\Contracts\MappingContract;
use Atom\DI\Contracts\MappingItemContract;
use Atom\DI\Exceptions\ContainerException;

class Mapping implements MappingContract
{

    private $map = [];

    /**
     * Array filled with the keys of all mapped entities
     * @return array<string>
     */
    public function getMappedEntities(): array
    {
        return array_keys($this->map);
    }

    /**
     * @param string $key
     * @return MappingItem
     * @throws ContainerException
     */
    public function getMappingFor(string $key): MappingItemContract
    {
        if (!$this->hasMappingFor($key)) {
            throw new ContainerException("Unable to find a valid mapping for [$key]");
        }
        return $this->map[$key];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasMappingFor(string $key): bool
    {
        return array_key_exists($key, $this->map);
    }

    /**
     * @param MappingItemContract $mappingItem
     * @return void
     */
    public function add(MappingItemContract $mappingItem)
    {
        $this->map[$mappingItem->getMappedEntityKey()] = $mappingItem;
    }
}
