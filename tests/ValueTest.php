<?php


namespace Oxygen\DI\Test;

use Oxygen\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Oxygen\DI\Extraction\ValueExtractor;
use Oxygen\DI\Value;

class ValueTest extends BaseTestCase
{
    public function testGetExtractorClassName()
    {
        $this->assertEquals(ValueExtractor::class, (new Value("foo"))->getExtractorClassName());
    }

    public function testGetExtractionParameter()
    {
        $storable = new Value("foo");
        $this->assertInstanceOf(ValueExtractionParameter::class, $storable->getExtractionParameter());
        $this->assertEquals("foo", $storable->getExtractionParameter()->getValue());
    }

    public function testWithExtractionParameter()
    {
        $storable = new Value("foo");
        $extractionParameter = new ValueExtractionParameter("bar");
        $this->assertNotEquals($storable->getExtractionParameter(), $extractionParameter);
        $storable->withExtractionParameter($extractionParameter);
        $this->assertEquals($storable->getExtractionParameter(), $extractionParameter);
    }
    public function testGetValue()
    {
        $storable = new Value("foo");
        $this->assertEquals("foo", $storable->getValue());
    }

    public function testSetValue()
    {
        $storable = new Value("foo");
        $storable->setValue("bar");
        $this->assertEquals("bar", $storable->getValue());
    }
}
