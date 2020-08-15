<?php


namespace Atom\DI\Test;

use Atom\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Atom\DI\Extraction\ValueExtractor;
use Atom\DI\Definitions\Value;

class ValueTest extends BaseTestCase
{
    public function testGetExtractorClassName()
    {
        $this->assertEquals(ValueExtractor::class, (new Value("foo"))->getExtractorClassName());
    }

    public function testGetExtractionParameter()
    {
        $definition = new Value("foo");
        $this->assertInstanceOf(ValueExtractionParameter::class, $definition->getExtractionParameter());
        $this->assertEquals("foo", $definition->getExtractionParameter()->getValue());
    }

    public function testWithExtractionParameter()
    {
        $definition = new Value("foo");
        $extractionParameter = new ValueExtractionParameter("bar");
        $this->assertNotEquals($definition->getExtractionParameter(), $extractionParameter);
        $definition->withExtractionParameter($extractionParameter);
        $this->assertEquals($definition->getExtractionParameter(), $extractionParameter);
    }
    public function testGetValue()
    {
        $definition = new Value("foo");
        $this->assertEquals("foo", $definition->getValue());
    }

    public function testSetValue()
    {
        $definition = new Value("foo");
        $definition->setValue("bar");
        $this->assertEquals("bar", $definition->getValue());
    }
}
