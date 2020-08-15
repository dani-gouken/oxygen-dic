<?php
namespace Atom\DI\Test\Extraction;

use Atom\DI\Exceptions\CircularDependencyException;
use Atom\DI\Exceptions\ContainerException;
use Atom\DI\Exceptions\NotFoundException;
use Atom\DI\Exceptions\StorageNotFoundException;
use Atom\DI\Exceptions\UnsupportedInvokerException;
use Atom\DI\Extraction\ContainerExtractor;
use Atom\DI\Extraction\ExtractionParameters\AbstractExtractionParameter;
use Atom\DI\Extraction\ExtractionParameters\ContainerExtractionParameter;
use Atom\DI\Test\BaseTestCase;
use Atom\DI\Definitions\Value;

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
