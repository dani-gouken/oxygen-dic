<?php
namespace Oxygen\DI\Test;

use Oxygen\DI\DIC;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{

    public function getContainer()
    {
        return new DIC();
    }
}
