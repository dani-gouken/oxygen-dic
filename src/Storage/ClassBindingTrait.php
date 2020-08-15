<?php


namespace Oxygen\DI\Storage;

use Oxygen\DI\Contracts\DefinitionContract;
use Oxygen\DI\Definitions\BuildObject;
use Oxygen\DI\Definitions\Value;

/**
 * Trait ClassBindingTrait
 * @package Oxygen\DI\Storage
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
