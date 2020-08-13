<?php


namespace Oxygen\DI\Test;

use Oxygen\DI\Extraction\ContainerExtractor;
use Oxygen\DI\Extraction\ExtractionParameters\ContainerExtractionParameter;
use Oxygen\DI\Definitions\Get;

class GetTest extends BaseTestCase
{
    public function testGetExtractorClassName()
    {
        $this->assertEquals(ContainerExtractor::class, (new Get("foo"))->getExtractorClassName());
    }

    public function testGetExtractionParameter()
    {
        $definition = new Get("foo");
        $this->assertInstanceOf(ContainerExtractionParameter::class, $definition->getExtractionParameter());
        $this->assertEquals("foo", $definition->getExtractionParameter()->getExtractionKey());
    }

    public function testWithExtractionParameter()
    {
        $definition = new Get("foo");
        $extractionParameter = new ContainerExtractionParameter("bar");
        $this->assertNotEquals($definition->getExtractionParameter(), $extractionParameter);
        $definition->withExtractionParameter($extractionParameter);
        $this->assertEquals($definition->getExtractionParameter(), $extractionParameter);
    }

}
