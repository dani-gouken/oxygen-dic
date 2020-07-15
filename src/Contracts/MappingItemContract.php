<?php


namespace Oxygen\DI\Contracts;

interface MappingItemContract extends ArraySerializable
{
    public function getMappedEntityKey();

    public function getStorable(): StorableContract;
}
