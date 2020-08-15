<?php


namespace Atom\DI\Test\Extraction;

use Atom\DI\Definitions\Value;
use Atom\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Atom\DI\Extraction\ExtractionParameters\WildcardExtractionParameter;
use Atom\DI\Extraction\WildcardExtractor;
use Atom\DI\Test\BaseTestCase;
use Atom\DI\Test\Misc\Dummy1;

class WildcardExtractorTest extends BaseTestCase
{
    public function testIsValidExtractionParameter()
    {
        $extractor = new WildcardExtractor();
        $this->assertTrue($extractor->isValidExtractionParameter(
            new WildcardExtractionParameter("foo", "bar", "bae")
        ));
        $this->assertFalse($extractor->isValidExtractionParameter(new ValueExtractionParameter("bar")));
    }

    public function testExtract()
    {
        $container = $this->getContainer();
        $container->wildcards()->store(
            $pattern = "Atom\DI\Test\Fixtures\*",
            $container->as()->wildcardFor($replacement = "Atom\DI\Test\Misc\*")
        );
        $extractor = $container->getExtractor(WildcardExtractor::class);
        $this->assertInstanceOf(
            Dummy1::class,
            $extractor->extract(
                new WildcardExtractionParameter(
                    'Atom\DI\Test\Misc\Dummy1',
                    $pattern,
                    $replacement
                ),
                $container
            )
        );
    }
}
