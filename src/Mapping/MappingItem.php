<?php


namespace Atom\DI\Mapping;

use Atom\DI\Contracts\MappingItemContract;
use Atom\DI\Contracts\DefinitionContract;

class MappingItem implements MappingItemContract
{
    /**
     * @var string
     */
    private $key;
    /**
     * @var DefinitionContract
     */
    private $definition;

    public function __construct(string $key, DefinitionContract $definition)
    {
        $this->key = $key;
        $this->definition = $definition;
    }

    public function getMappedEntityKey()
    {
        return $this->key;
    }

    public function getDefinition():DefinitionContract
    {
        return $this->definition;
    }
}
