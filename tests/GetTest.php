<?php


namespace Oxygen\DI\Test;

use Oxygen\DI\Extraction\ContainerExtractor;
use Oxygen\DI\Extraction\ExtractionParameters\ContainerExtractionParameter;
use Oxygen\DI\Get;

class GetTest extends BaseTestCase
{
    public function testGetExtractorClassName()
    {
        $this->assertEquals(ContainerExtractor::class, (new Get("foo"))->getExtractorClassName());
    }

    public function testGetExtractionParameter()
    {
        $storable = new Get("foo");
        $this->assertInstanceOf(ContainerExtractionParameter::class, $storable->getExtractionParameter());
        $this->assertEquals("foo", $storable->getExtractionParameter()->getExtractionKey());
    }

    public function testWithExtractionParameter()
    {
        $storable = new Get("foo");
        $extractionParameter = new ContainerExtractionParameter("bar");
        $this->assertNotEquals($storable->getExtractionParameter(), $extractionParameter);
        $storable->withExtractionParameter($extractionParameter);
        $this->assertEquals($storable->getExtractionParameter(), $extractionParameter);
    }

}
