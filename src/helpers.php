<?php

use Oxygen\DI\BuildObject;
use Oxygen\DI\CallMethod;
use Oxygen\DI\Get;
use Oxygen\DI\Value;

function buildObject(string $className, array $constructorParameter = []){
    return new BuildObject($className,$constructorParameter);
}

function callMethod(string $methodName = "__invoke", array $parameters = []){
    return new CallMethod($methodName,$parameters);
}

function callInvoke(array $parameters = []){
    return callMethod("__invoke",$parameters);
}

function callFunction($callable,array $parameters = []){
    return callFunction($callable,$parameters);
}

function get(string $key){
    return new Get($key);
}

function value($value){
    return new Value($value);
}