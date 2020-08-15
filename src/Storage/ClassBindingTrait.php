<?php


namespace Atom\DI\Storage;

use Atom\DI\Contracts\DefinitionContract;
use Atom\DI\Definitions\BuildObject;
use Atom\DI\Definitions\Value;

/**
 * Trait ClassBindingTrait
 * @package Atom\DI\Storage
 */
trait ClassBindingTrait
{
    abstract public function store(string $key, DefinitionContract $definition);
    /**
     * @param string $className
     * @return BuildObject
     */
    public function bindClass(string $className): BuildObject
    {
        $this->store($className, $definition =  new BuildObject($className));
        return $definition;
    }

    /**
     * @param object $object
     * @return Value
     */
    public function bindInstance(object $object): Value
    {
        $this->store(get_class($object), $definition =  new Value($object));
        return $definition;
    }
}
