<?php

namespace Oxygen\DI\Test\Misc\CircularDependency;

class CDDummy1
{
    public function __construct(CDDummy0 $dm)
    {
    }
}
