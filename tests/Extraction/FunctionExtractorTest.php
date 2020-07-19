<?php

namespace Oxygen\DI\Test\Extraction;

use Oxygen\DI\Contracts\ExtractionParameterContract;
use Oxygen\DI\Exceptions\CircularDependencyException;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Extraction\ExtractionParameters\FunctionExtractionParameter;
use Oxygen\DI\Extraction\ExtractionParameters\MethodExtractionParameter;
use Oxygen\DI\Extraction\FunctionExtractor;
use Oxygen\DI\Test\BaseTestCase;
use ReflectionException;
use function Oxygen\DI\Test\Misc\returnBar;

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
                new FunctionExtractionParameter("Oxygen\\DI\\Test\\Misc\\returnFoo"),
                $this->getContainer()
            ),
        );
    }
}
