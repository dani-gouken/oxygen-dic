<?php


namespace Oxygen\DI;

class StorableFactory
{
    public function instantiateOf(string $className, array $constructorParameter = [])
    {
        return new BuildObject($className, $constructorParameter);
    }

    public function storedValue(string $key)
    {
        return new Get($key);
    }

    public function value($value)
    {
        return new Value($value);
    }

    public function callTo($callable, $parameters = [])
    {
        return new CallableStorableFactory($callable, $parameters);
    }
}
