<?php

namespace Oxygen\DI\Test\Misc\CircularDependency;

class CDDummy2
{
    public function __construct(CDDummy1 $dm)
    {
    }
}
