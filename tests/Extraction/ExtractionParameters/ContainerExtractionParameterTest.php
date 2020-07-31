<?php


namespace Oxygen\DI\Test\Extraction\ExtractionParameters;

use Oxygen\DI\Extraction\ExtractionParameters\ContainerExtractionParameter;
use Oxygen\DI\Test\BaseTestCase;

class ContainerExtractionParameterTest extends BaseTestCase
{
    public function makeParameter(string $key)
    {
        return new ContainerExtractionParameter($key);
    }

    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(ContainerExtractionParameter::class, $this->makeParameter("foo"));
    }

    public function testGetExtractionKey()
    {
        $this->assertEquals(
            $this->makeParameter("foo")->getExtractionKey(),
            "foo"
        );
    }
}
