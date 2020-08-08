<?php


namespace Oxygen\DI\Test;

use Oxygen\DI\CallFunction;
use Oxygen\DI\Extraction\ExtractionParameters\FunctionExtractionParameter;
use Oxygen\DI\Extraction\FunctionExtractor;

class CallFunctionTest extends BaseTestCase
{
    private function makeStorable(array $params = []):CallFunction
    {
        return new CallFunction(function () {
            return "foo";
        }, $params);
    }

    public function testGetExtractorClassName()
    {
        $this->assertEquals(FunctionExtractor::class, $this->makeStorable()->getExtractorClassName());
    }

    public function testGetExtractionParameter()
    {
        $storable = $this->makeStorable(["foo"=>"bar"]);
        $this->assertInstanceOf(FunctionExtractionParameter::class, $storable->getExtractionParameter());
        $this->assertEquals(["foo"=>"bar"], $storable->getExtractionParameter()->getParameters());
    }

    public function testWithExtractionParameter()
    {
        $storable = $this->makeStorable();
        $extractionParameter = new FunctionExtractionParameter("foo");
        $storable->withExtractionParameter($extractionParameter);
        $this->assertInstanceOf(FunctionExtractionParameter::class, $storable->getExtractionParameter());
        $this->assertEquals($extractionParameter, $storable->getExtractionParameter());
    }
}
