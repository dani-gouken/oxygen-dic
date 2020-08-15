<?php


namespace Atom\DI\Test;

use Atom\DI\Definitions\CallMethod;
use Atom\DI\Definitions\Value;
use Atom\DI\Extraction\ExtractionParameters\MethodExtractionParameter;
use Atom\DI\Extraction\MethodExtractor;
use Atom\DI\Test\Misc\Dummy1;

class CallMethodTest extends BaseTestCase
{
    public function makeDefinition(): CallMethod
    {
        return new CallMethod();
    }

    public function testGetExtractionParameter()
    {
        $definition = $this->makeDefinition();
        $definition->on($instance = new Dummy1());
        $this->assertInstanceOf(MethodExtractionParameter::class, $definition->getExtractionParameter());
        $this->assertEquals($instance, $definition->getExtractionParameter()->getClass());
    }

    public function testGetExtractionParameterThrowIfNoClassWasSpecified()
    {
        $definition = $this->makeDefinition();
        $this->expectException(\InvalidArgumentException::class);
        $definition->getExtractionParameter();
    }

    public function testExtractorClassName()
    {
        $definition = $this->makeDefinition();
        $this->assertEquals(MethodExtractor::class, $definition->getExtractorClassName());
    }

    public function testOn()
    {
        $definition = $this->makeDefinition();
        $definition->on($instance = new Dummy1());
        $this->assertEquals($instance, $definition->getExtractionParameter()->getClass());
    }

    public function testWithExtractionParameter()
    {
        $definition = $this->makeDefinition();
        $extractionParameter = new MethodExtractionParameter("foo", "bar");
        $definition->withExtractionParameter($extractionParameter);
        $this->assertInstanceOf(MethodExtractionParameter::class, $definition->getExtractionParameter());
        $this->assertEquals($extractionParameter, $definition->getExtractionParameter());
    }

    public function testWithParameter()
    {
        $definition = $this->makeDefinition();
        $definition->on(new Dummy1());
        $definition->withParameter("foo", $value1= new Value("bar"));
        $definition->withParameter("bar", $value2= new Value("baz"));

        $this->assertTrue($definition->getExtractionParameter()->getParameterMapping()->hasMappingFor('foo'));
        $this->assertTrue($definition->getExtractionParameter()->getParameterMapping()->hasMappingFor('bar'));
        $this->assertEquals(
            $definition->getExtractionParameter()->getParameterMapping()->getMappingFor('foo')->getDefinition(),
            $value1
        );
        $this->assertEquals(
            $definition->getExtractionParameter()->getParameterMapping()->getMappingFor('bar')->getDefinition(),
            $value2
        );
    }
}
