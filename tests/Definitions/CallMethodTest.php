<?php


namespace Oxygen\DI\Test;

use Oxygen\DI\Definitions\CallMethod;
use Oxygen\DI\Extraction\ExtractionParameters\MethodExtractionParameter;
use Oxygen\DI\Extraction\MethodExtractor;
use Oxygen\DI\Test\Misc\Dummy1;

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
}
