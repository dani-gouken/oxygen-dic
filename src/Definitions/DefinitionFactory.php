<?php


namespace Atom\DI\Definitions;

class DefinitionFactory
{
    public function instanceOf(string $className, array $constructorParameter = []):BuildObject
    {
        return new BuildObject($className, $constructorParameter);
    }

    public function get(string $key):Get
    {
        return new Get($key);
    }

    public function value($value):Value
    {
        return new Value($value);
    }
    public function object(object $object): Value
    {
        return new Value($object);
    }

    public function wildcardFor(string $replacement):Wildcard
    {
        return new Wildcard($replacement);
    }

    public function callTo($callable, $parameters = [])
    {
        return new CallableDefinitionFactory($callable, $parameters);
    }
}
