<?php

use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Extraction\ContainerExtractor;
use Oxygen\DI\Extraction\ExtractionParameters\AbstractExtractionParameter;
use Oxygen\DI\Extraction\ExtractionParameters\ContainerExtractionParameter;
use Oxygen\DI\Test\BaseTestCase;

class ContainerExtractorTest extends  BaseTestCase
{
    private function makeExtractor(): ContainerExtractor
    {
        return new ContainerExtractor();
    }
    public function testIsValidExtractionParameter()
    {
        $invalidExtractionParameter = $this->createMock(AbstractExtractionParameter::class);
        $validExtractionParameter = new ContainerExtractionParameter("test");
        $extractor = $this->makeExtractor();
        $this->assertTrue($extractor->isValidExtractionParameter($validExtractionParameter));
        $this->assertFalse($extractor->isValidExtractionParameter($invalidExtractionParameter));
    }

    public function testExtract()
    {
        $dic = $this->getContainer();
        $dic->value()->store("answer", value("42"));
        $extractor = $this->makeExtractor();
        $this->assertEquals("42", $extractor->extract(new ContainerExtractionParameter("answer"), $dic));
        $this->expectException(NotFoundException::class);
        $extractor->extract(new ContainerExtractionParameter("foo"), $dic);
    }
}
