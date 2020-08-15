<?php

namespace Oxygen\DI\Test\Definitions;

use Oxygen\DI\Definitions\BuildObject;
use Oxygen\DI\Definitions\CallableDefinitionFactory;
use Oxygen\DI\Definitions\DefinitionFactory;
use Oxygen\DI\Definitions\Get;
use Oxygen\DI\Definitions\Value;
use Oxygen\DI\Definitions\Wildcard;
use Oxygen\DI\Test\BaseTestCase;
use Oxygen\DI\Test\Misc\Dummy1;

class DefinitionFactoryTest extends BaseTestCase
{
    public function makeFactory():DefinitionFactory
    {
        return new DefinitionFactory();
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(
            BuildObject::class,
            $definition = $this->makeFactory()->instanceOf("foo")
        );
        $this->assertEquals("foo", $definition->getExtractionParameter()->getClassName());
    }

    public function testGet()
    {
         $this->assertInstanceOf(
             Get::class,
             $definition = $this->makeFactory()->get("foo")
         );
        $this->assertEquals("foo", $definition->getExtractionParameter()->getExtractionKey());
    }

    public function testValue()
    {
         $this->assertInstanceOf(
             Value::class,
             $definition = $this->makeFactory()->value("foo")
         );
        $this->assertEquals("foo", $definition->getExtractionParameter()->getValue());
    }

    public function testObject()
    {
         $this->assertInstanceOf(
             Value::class,
             $definition = $this->makeFactory()->object($object = new Dummy1())
         );
        $this->assertEquals($object, $definition->getExtractionParameter()->getValue());
    }

    public function testWildcard()
    {
         $this->assertInstanceOf(
             Wildcard::class,
             $definition = $this->makeFactory()->wildcardFor("foo")
         );
        $this->assertEquals("foo", $definition->getReplacement());
    }

    public function testCallTo()
    {
         $this->assertInstanceOf(
             CallableDefinitionFactory::class,
             $definition = $this->makeFactory()->callTo("foo")
         );
    }


}
