<?php


namespace Atom\DI\Contracts;

interface MappingItemContract
{
    public function getMappedEntityKey();

    public function getDefinition(): DefinitionContract;
}
