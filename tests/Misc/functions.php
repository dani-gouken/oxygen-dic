<?php

namespace Atom\DI\Test\Misc;

function returnFoo()
{
    return "foo";
}

function returnBar()
{
    return "bar";
}

function returnDefaultValue($defaultValue = "DefaultValue")
{
    return $defaultValue;
}

function returnValue($value)
{
    return $value;
}

function returnDummy2(Dummy2 $dummy2)
{
    return $dummy2;
}

function returnDummy1(Dummy1 $dummy1)
{
    return $dummy1;
}

