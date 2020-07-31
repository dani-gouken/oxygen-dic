<?php


namespace Oxygen\DI\Contracts;

interface MappingItemContract
{
    public function getMappedEntityKey();

    public function getStorable(): StorableContract;
}
