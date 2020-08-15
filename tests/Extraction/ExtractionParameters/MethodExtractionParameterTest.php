<?php


namespace Atom\DI\Test\Extraction\ExtractionParameters;

use Atom\DI\Extraction\ExtractionParameters\MethodExtractionParameter;
use Atom\DI\Test\BaseTestCase;
use Atom\DI\Test\Misc\Dummy1;
use Atom\DI\Definitions\Value;

class MethodExtractionParameterTest extends BaseTestCase
{
    public function makeParameter($class, string $method, array $parameters = [])
    {
        return new MethodExtractionParameter($class, $method, $parameters);
    }

    public function testItCantBeInstantiated()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->makeParameter(1, "foo");
    }

    public function testItCanBeInstantiatedWithAnObject()
    {
        $param = $this->makeParameter(new Dummy1(), "foo");
        $this->assertInstanceOf(MethodExtractionParameter::class, $param);
        $param = $this->makeParameter(new Value("foo"), "bar");
        $this->assertInstanceOf(MethodExtractionParameter::class, $param);
        $param = $this->makeParameter("foo", "bar");
        $this->assertInstanceOf(MethodExtractionParameter::class, $param);
    }

    public function testGetExtractionKey()
    {
        $param = $this->makeParameter(new Dummy1(), "foo");
        $this->assertEquals(Dummy1::class . "::foo", $param->getExtractionKey());
        $param = $this->makeParameter(new Value("foo"), "bar");
        $this->assertEquals("foo::bar", $param->getExtractionKey());
        $param = $this->makeParameter("foo", "bar");
        $this->assertEquals("foo::bar", $param->getExtractionKey());
    }

    public function testGetParameters()
    {
        $param = $this->makeParameter("foo", "bar", $parameters = ["foo" => "bar"]);
        $this->assertEquals($param->getParameters(), $parameters);
    }

    public function testGetClassName()
    {
        $param = $this->makeParameter(new Dummy1(), "foo");
        $this->assertEquals(Dummy1::class, $param->getClassName());
        $param = $this->makeParameter(new Value("foo"), "bar");
        $this->assertEquals("foo", $param->getClassName());
        $param = $this->makeParameter("foo", "bar");
        $this->assertEquals("foo", $param->getClassName());
    }

    public function testGetClass()
    {
        $param = $this->makeParameter($object = new Dummy1(), "foo");
        $this->assertEquals($object, $param->getClass());
        $param = $this->makeParameter($definition = new Value("foo"), "bar");
        $this->assertEquals($definition, $param->getClass());
        $param = $this->makeParameter("foo", "bar");
        $this->assertEquals("foo", $param->getClass());
    }

    public function testClassIsString()
    {
        $param = $this->makeParameter($object = new Dummy1(), "foo");
        $this->assertFalse($param->classIsString());
        $param = $this->makeParameter($definition = new Value("foo"), "bar");
        $this->assertFalse($param->classIsString());
        $param = $this->makeParameter("foo", "bar");
        $this->assertTrue($param->classIsString());
    }

    public function testSetParameters()
    {
         $param = $this->makeParameter("foo", "bar");
        $this->assertEmpty($param->getParameters());
        $param->setParameters($params = ["foo"=>"bar"]);
        $this->assertEquals($params, $param->getParameters());
    }
}
