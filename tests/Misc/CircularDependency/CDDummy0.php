<?php

namespace Atom\DI\Test\Misc\CircularDependency;

class CDDummy0
{
    public function __construct(CDDummy1 $dm)
    {
    }
}
