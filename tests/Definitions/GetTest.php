<?php


namespace Atom\DI\Test;

use Atom\DI\Extraction\ContainerExtractor;
use Atom\DI\Extraction\ExtractionParameters\ContainerExtractionParameter;
use Atom\DI\Definitions\Get;

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
