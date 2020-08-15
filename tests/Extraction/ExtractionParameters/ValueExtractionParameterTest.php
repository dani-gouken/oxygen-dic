<?php


namespace Atom\DI\Test\Extraction\ExtractionParameters;

use Atom\DI\Extraction\ExtractionParameters\ValueExtractionParameter;
use Atom\DI\Test\BaseTestCase;
use Atom\DI\Test\Misc\Dummy1;

class ValueExtractionParameterTest extends BaseTestCase
{
    public function makeParameter($value): ValueExtractionParameter
    {
        return new ValueExtractionParameter($value);
    }

    public function testGetExtractionKey()
    {
        $param = $this->makeParameter("foo");
        $this->assertEquals("foo", $param->getExtractionKey());

        $param = $this->makeParameter(1);
        $this->assertEquals(1, $param->getExtractionKey());
        $param = $this->makeParameter(true);
        $this->assertEquals(true, $param->getExtractionKey());
        $param = $this->makeParameter(new Dummy1());
        $this->assertEquals(Dummy1::class, $param->getExtractionKey());
        $param = $this->makeParameter(function () {
        });
        $this->assertStringContainsString("Closure", $param->getExtractionKey());
        $param = $this->makeParameter([]);
        $this->assertStringContainsString("array", $param->getExtractionKey());
    }

    public function testGetValue()
    {
        $param = $this->makeParameter("foo");
        $this->assertEquals("foo", $param->getValue());
    }

    public function testSetValue()
    {
        $param = $this->makeParameter("foo");
        $this->assertEquals("foo", $param->getValue());

        $param->setValue("bar");
        $this->assertEquals("bar", $param->getValue());
    }
}
