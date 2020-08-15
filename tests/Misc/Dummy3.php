<?php


namespace Atom\DI\Test\Misc;

class Dummy3
{
    public function __invoke()
    {
        return "foo";
    }

    public function getBar(string $bar):string
    {
        return $bar;
    }
}
