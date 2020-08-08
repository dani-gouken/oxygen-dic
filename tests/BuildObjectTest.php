<?php


namespace Oxygen\DI\Test;

use Oxygen\DI\BuildObject;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Extraction\ExtractionParameters\ObjectExtractionParameter;
use Oxygen\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Oxygen\DI\Extraction\ObjectExtractor;

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
        $storable = new BuildObject("foo", ["bar" => "baz"]);
        $this->assertInstanceOf(ObjectExtractionParameter::class, $storable->getExtractionParameter());
        $this->assertEquals("foo", $storable->getExtractionParameter()->getClassName());
        $this->assertEquals(["bar" => "baz"], $storable->getExtractionParameter()->getConstructorArgs());
    }

    public function testWithExtractionParameters()
    {
        $storable = new BuildObject("foo", ["bar" => "baz"]);
        $this->assertEquals("foo", $storable->getExtractionParameter()->getClassName());
        $this->assertEquals(["bar" => "baz"], $storable->getExtractionParameter()->getConstructorArgs());
        $storable->withExtractionParameter(new ObjectExtractionParameter("bar", ["foo" => "baz"]));
        $this->assertEquals("bar", $storable->getExtractionParameter()->getClassName());
        $this->assertEquals(["foo" => "baz"], $storable->getExtractionParameter()->getConstructorArgs());
    }

    public function testWithConstructorParameters()
    {
        $storable = new BuildObject("foo", ["bar" => "baz"]);
        $this->assertEquals(["bar" => "baz"], $storable->getExtractionParameter()->getConstructorArgs());
        $storable->withConstructorParameters(["foo"=>"bar"]);
        $this->assertEquals(["foo" => "bar"], $storable->getExtractionParameter()->getConstructorArgs());
    }
}
