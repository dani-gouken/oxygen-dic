<?php

namespace Atom\DI\Test\Extraction;

use Atom\DI\Contracts\ExtractionParameterContract;
use Atom\DI\Exceptions\CircularDependencyException;
use Atom\DI\Exceptions\ContainerException;
use Atom\DI\Exceptions\NotFoundException;
use Atom\DI\Extraction\ExtractionParameters\FunctionExtractionParameter;
use Atom\DI\Extraction\ExtractionParameters\MethodExtractionParameter;
use Atom\DI\Extraction\FunctionExtractor;
use Atom\DI\Test\BaseTestCase;
use ReflectionException;
use function Atom\DI\Test\Misc\returnBar;

class FunctionExtractorTest extends BaseTestCase
{
    private function makeExtractor(): FunctionExtractor
    {
        return new FunctionExtractor();
    }
    public function testIsValidExtractionParameter()
    {
        $extractor = $this->makeExtractor();
        $this->assertTrue($extractor->isValidExtractionParameter(new FunctionExtractionParameter("jhon")));
        $this->assertFalse($extractor->isValidExtractionParameter(new MethodExtractionParameter("foo", "bar")));
        $this->assertFalse($extractor->isValidExtractionParameter(
            $this->createMock(ExtractionParameterContract::class)
        ));
    }

    /**
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function testItExtract()
    {
        $extractor = $this->makeExtractor();
        //Closure
        $this->assertEquals("bar", $extractor->extract(new FunctionExtractionParameter(function () {
            return returnBar();
        }), $this->getContainer()), "bar");
        //Function
        $this->assertEquals(
            "foo",
            $extractor->extract(
                new FunctionExtractionParameter("Atom\\DI\\Test\\Misc\\returnFoo"),
                $this->getContainer()
            ),
        );
    }
}
