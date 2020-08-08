<?php


namespace Oxygen\DI\Test;

use Oxygen\DI\CallMethod;
use Oxygen\DI\Extraction\ExtractionParameters\MethodExtractionParameter;
use Oxygen\DI\Extraction\MethodExtractor;
use Oxygen\DI\Test\Misc\Dummy1;

class CallMethodTest extends BaseTestCase
{
    public function makeStorable(): CallMethod
    {
        return new CallMethod();
    }

    public function testGetExtractionParameter()
    {
        $storable  = $this->makeStorable();
        $storable->on($instance = new Dummy1());
        $this->assertInstanceOf(MethodExtractionParameter::class, $storable->getExtractionParameter());
        $this->assertEquals($instance, $storable->getExtractionParameter()->getClass());
    }

    public function testGetExtractionParameterThrowIfNoClassWasSpecified()
    {
        $storable  = $this->makeStorable();
        $this->expectException(\InvalidArgumentException::class);
        $storable->getExtractionParameter();
    }

    public function testExtractorClassName()
    {
        $storable = $this->makeStorable();
        $this->assertEquals(MethodExtractor::class, $storable->getExtractorClassName());
    }

    public function testOn()
    {
        $storable  = $this->makeStorable();
        $storable->on($instance = new Dummy1());
        $this->assertEquals($instance, $storable->getExtractionParameter()->getClass());
    }

    public function testWithExtractionParameter()
    {
        $storable = $this->makeStorable();
        $extractionParameter = new MethodExtractionParameter("foo", "bar");
        $storable->withExtractionParameter($extractionParameter);
        $this->assertInstanceOf(MethodExtractionParameter::class, $storable->getExtractionParameter());
        $this->assertEquals($extractionParameter, $storable->getExtractionParameter());
    }
}
