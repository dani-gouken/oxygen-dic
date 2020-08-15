<?php


namespace Oxygen\DI\Test\Definitions;

use Oxygen\DI\Definitions\CallableDefinitionFactory;
use Oxygen\DI\Definitions\CallFunction;
use Oxygen\DI\Definitions\CallMethod;
use Oxygen\DI\Test\BaseTestCase;

class CallableDefinitionFactoryTest extends BaseTestCase
{
    public function makeFactory($callable = null): CallableDefinitionFactory
    {
        return new CallableDefinitionFactory($callable ?? function () {
        });
    }

    public function testFunction()
    {
        $this->assertInstanceOf(CallFunction::class, $this->makeFactory()->function());
    }

    public function testMethod()
    {
        $this->assertInstanceOf(CallMethod::class, $this->makeFactory("foo")->method());
    }
}
