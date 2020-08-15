<?php


namespace Atom\DI\Definitions;

class CallableDefinitionFactory
{
    private $parameters;
    private $callable;
    public function __construct($callable, array $parameters = [])
    {
        $this->callable = $callable;
        $this->parameters = $parameters;
    }

    /**
     * @return CallFunction
     */
    public function function(): CallFunction
    {
        return new CallFunction($this->callable, $this->parameters);
    }

    /**
     * @return CallMethod
     */
    public function method(): CallMethod
    {
        return new CallMethod($this->callable, $this->parameters);
    }
}
