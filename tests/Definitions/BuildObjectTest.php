<?php


namespace Atom\DI\Test;

use Atom\DI\Definitions\BuildObject;
use Atom\DI\Extraction\ExtractionParameters\ObjectExtractionParameter;
use Atom\DI\Extraction\ObjectExtractor;

class BuildObjectTest extends BaseTestCase
{
    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(BuildObject::class, new BuildObject("foo"));
    }

    public function testGetExtractorClassName()
    {
        $this->assertEquals(ObjectExtractor::class, (new BuildObject("foo"))->getExtractorClassName());
    }

    public function testGetExtractionParameter()
    {
        $definition = new BuildObject("foo", ["bar" => "baz"]);
        $this->assertInstanceOf(ObjectExtractionParameter::class, $definition->getExtractionParameter());
        $this->assertEquals("foo", $definition->getExtractionParameter()->getClassName());
        $this->assertEquals(["bar" => "baz"], $definition->getExtractionParameter()->getConstructorArgs());
    }

    public function testWithExtractionParameters()
    {
        $definition = new BuildObject("foo", ["bar" => "baz"]);
        $this->assertEquals("foo", $definition->getExtractionParameter()->getClassName());
        $this->assertEquals(["bar" => "baz"], $definition->getExtractionParameter()->getConstructorArgs());
        $definition->withExtractionParameter(new ObjectExtractionParameter("bar", ["foo" => "baz"]));
        $this->assertEquals("bar", $definition->getExtractionParameter()->getClassName());
        $this->assertEquals(["foo" => "baz"], $definition->getExtractionParameter()->getConstructorArgs());
    }

    public function testWithConstructorParameters()
    {
        $definition = new BuildObject("foo", ["bar" => "baz"]);
        $this->assertEquals(["bar" => "baz"], $definition->getExtractionParameter()->getConstructorArgs());
        $definition->withConstructorParameters(["foo" => "bar"]);
        $this->assertEquals(["foo" => "bar"], $definition->getExtractionParameter()->getConstructorArgs());
    }
}
