<?php

namespace Oxygen\DI\Test\Misc\CircularDependency;

class CDDummy0
{
    public function __construct(CDDummy1 $dm)
    {
    }
}
