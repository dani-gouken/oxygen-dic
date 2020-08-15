<?php


namespace Atom\DI\Test\Extraction;

use Atom\DI\Extraction\ExtractionParameters\ContainerExtractionParameter;
use Atom\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Atom\DI\Extraction\ValueExtractor;
use Atom\DI\Test\BaseTestCase;
use Atom\DI\Test\Misc\Dummy1;

class ValueExtractorTest extends BaseTestCase
{
    private function makeExtractor():ValueExtractor
    {
        return new ValueExtractor();
    }

    public function testIsValidExtractionParameter()
    {
        $extractor = $this->makeExtractor();
        $this->assertTrue($extractor->isValidExtractionParameter(new ValueExtractionParameter("foo")));
        $this->assertFalse($extractor->isValidExtractionParameter(new ContainerExtractionParameter("bar")));
    }

    public function testItExtract()
    {
        $extractor = $this->makeExtractor();
        $container = $this->getContainer();
        $function = function () {
            return "john doe";
        };
        $this->assertEquals($function, $extractor->extract(new ValueExtractionParameter($function), $container));
        $this->assertEquals(42, $extractor->extract(new ValueExtractionParameter(42), $container));
        $this->assertEquals(
            $dummy = new Dummy1(),
            $extractor->extract(new ValueExtractionParameter($dummy), $container)
        );
        $this->assertEquals(
            "MUDA! MUDA!",
            $extractor->extract(new ValueExtractionParameter("MUDA! MUDA!"), $container)
        );
    }
}
