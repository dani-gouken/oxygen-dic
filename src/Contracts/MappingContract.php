<?php


namespace Atom\DI\Contracts;

interface MappingContract
{
    /**
     * Array filled with the keys of all mapped entities
     * @return array<string>
     */
    public function getMappedEntities(): array;

    /**
     * @param string $key
     * @return MappingItemContract
     */
    public function getMappingFor(string $key): MappingItemContract;

    /**
     * @param string $key
     * @return bool
     */
    public function hasMappingFor(string $key): bool;

    /**
     * @param MappingItemContract $mappingItem
     * @return bool
     */
    public function add(MappingItemContract $mappingItem);
}
