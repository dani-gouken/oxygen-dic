<?php
namespace Atom\DI\Test;

use Atom\DI\DIC;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{

    public function getContainer()
    {
        return new DIC();
    }
}
