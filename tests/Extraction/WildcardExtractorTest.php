<?php


namespace Oxygen\DI\Test\Extraction;

use Oxygen\DI\Definitions\Value;
use Oxygen\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Oxygen\DI\Extraction\ExtractionParameters\WildcardExtractionParameter;
use Oxygen\DI\Extraction\WildcardExtractor;
use Oxygen\DI\Test\BaseTestCase;
use Oxygen\DI\Test\Misc\Dummy1;

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
            $pattern = "Oxygen\DI\Test\Fixtures\*",
            $container->as()->wildcardFor($replacement = "Oxygen\DI\Test\Misc\*")
        );
        $extractor = $container->getExtractor(WildcardExtractor::class);
        $this->assertInstanceOf(
            Dummy1::class,
            $extractor->extract(
                new WildcardExtractionParameter(
                    'Oxygen\DI\Test\Misc\Dummy1',
                    $pattern,
                    $replacement
                ),
                $container
            )
        );
    }
}
