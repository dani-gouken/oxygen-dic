<?php
namespace Oxygen\DI\Test\Extraction;

use Oxygen\DI\Exceptions\CircularDependencyException;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Exceptions\StorageNotFoundException;
use Oxygen\DI\Exceptions\UnsupportedInvokerException;
use Oxygen\DI\Extraction\ContainerExtractor;
use Oxygen\DI\Extraction\ExtractionParameters\AbstractExtractionParameter;
use Oxygen\DI\Extraction\ExtractionParameters\ContainerExtractionParameter;
use Oxygen\DI\Test\BaseTestCase;
use Oxygen\DI\Definitions\Value;

class ContainerExtractorTest extends BaseTestCase
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

    /**
     * @throws NotFoundException
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws StorageNotFoundException
     * @throws UnsupportedInvokerException
     */
    public function testExtract()
    {
        $dic = $this->getContainer();
        $dic->values()->store("answer", new Value("42"));
        $extractor = $this->makeExtractor();
        $this->assertEquals("42", $extractor->extract(new ContainerExtractionParameter("answer"), $dic));
        $this->expectException(NotFoundException::class);
        $extractor->extract(new ContainerExtractionParameter("foo"), $dic);
    }
}
