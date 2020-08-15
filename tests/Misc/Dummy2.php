<?php

namespace Atom\DI\Test\Misc;

class Dummy2
{
    private $foo;
    private $bar;
    public function __construct(String $foo, string $bar = "bar")
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function getBar()
    {
        return $this->bar;
    }
}
