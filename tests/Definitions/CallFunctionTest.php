<?php


namespace Atom\DI\Test;

use Atom\DI\Definitions\CallFunction;
use Atom\DI\Extraction\ExtractionParameters\FunctionExtractionParameter;
use Atom\DI\Extraction\FunctionExtractor;

class CallFunctionTest extends BaseTestCase
{
    private function makeDefinition(array $params = []):CallFunction
    {
        return new CallFunction(function () {
            return "foo";
        }, $params);
    }

    public function testGetExtractorClassName()
    {
        $this->assertEquals(FunctionExtractor::class, $this->makeDefinition()->getExtractorClassName());
    }

    public function testGetExtractionParameter()
    {
        $definition = $this->makeDefinition(["foo"=>"bar"]);
        $this->assertInstanceOf(FunctionExtractionParameter::class, $definition->getExtractionParameter());
        $this->assertEquals(["foo"=>"bar"], $definition->getExtractionParameter()->getParameters());
    }

    public function testWithExtractionParameter()
    {
        $definition = $this->makeDefinition();
        $extractionParameter = new FunctionExtractionParameter("foo");
        $definition->withExtractionParameter($extractionParameter);
        $this->assertInstanceOf(FunctionExtractionParameter::class, $definition->getExtractionParameter());
        $this->assertEquals($extractionParameter, $definition->getExtractionParameter());
    }
}
