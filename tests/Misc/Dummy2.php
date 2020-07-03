<?php

namespace Oxygen\DI\Test\Misc;

class Dummy2
{
    private $foo;
    public function __construct(String $foo)
    {
        $this->foo = $foo;
    }

    public function getFoo()
    {
        return $this->foo;
    }
}
